<?php

namespace Ben\AssociationBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Httpfoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

use Ben\AssociationBundle\Entity\Rooms;
use Ben\AssociationBundle\Form\RoomsType;
use Ben\UserBundle\Entity\User;

use Ben\AssociationBundle\Pagination\Paginator;

/**
 * Rooms controller.
 *
 */
class RoomsController extends Controller
{
    /**
     * Lists all Rooms entities.
     * @Secure(roles="ROLE_MANAGER")
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entitiesLength = $em->getRepository('BenAssociationBundle:Rooms')->counter();
        return $this->render('BenAssociationBundle:Rooms:index.html.twig', array(
                'entitiesLength' => $entitiesLength[1]));
    }

    /**
     * ajax Lists Rooms entities.
     * @Secure(roles="ROLE_MANAGER")
     *
     */
    public function ajaxListAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $perPage = $request->get('perpage');
        $page = $request->get('page');
        $source = $request->get('source');
        $keyword = $request->get('keyword');
        $searchEntity = $request->get('searchEntity');
        $entities = $em->getRepository('BenAssociationBundle:Rooms')->search($perPage, $page, $keyword, $searchEntity);
        $pagination = (new Paginator())->setItems(count($entities), $perPage)->setPage($page)->toArray();
        return $this->render('BenAssociationBundle:Rooms:ajax_list.html.twig', array(
                    'entities' => $entities,
                    'pagination' => $pagination,
                    'source' => $source
                    ));
    }

    /**
     * json rooms entities.
     * @Secure(roles="ROLE_MANAGER")
     *
     */
    public function ajaxFilterListAction($floor)
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('BenAssociationBundle:Rooms')->findByFloor($floor);
        $entities = array_map(function($entity){
                        return array(
                            'id' => $entity->getId(),
                            'number' => $entity->getNumber());
                    }, $entities);
        $response = new Response(json_encode($entities));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * Finds and displays a Rooms entity.
     * @Secure(roles="ROLE_MANAGER")
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BenAssociationBundle:Rooms')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Rooms entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BenAssociationBundle:Rooms:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            ));
    }

    /**
     * Displays a form to create a new Rooms entity.
     * @Secure(roles="ROLE_MANAGER")
     *
     */
    public function newAction()
    {
        $entity = new Rooms();
        $form   = $this->createForm(new RoomsType(), $entity);

        return $this->render('BenAssociationBundle:Rooms:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new Rooms entity.
     * @Secure(roles="ROLE_MANAGER")
     *
     */
    public function createAction(Request $request)
    {
        $entity  = new Rooms();
        $form = $this->createForm(new RoomsType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('rooms_show', array('id' => $entity->getId())));
        }

        return $this->render('BenAssociationBundle:Rooms:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to create a mutiple Rooms entity.
     * @Secure(roles="ROLE_USER")
     *
     */
    public function newMultipleAction()
    {
        $entity = new Rooms();
        $form = $this->createFormBuilder($entity)
                ->add('type', 'choice', array('choices' => array('homme' => 'Homme','femme' => 'Femme'),
                    'required' => false,))
                ->add('hotel', null, array('label'  => 'Logement'))
            ->getForm();

        return $this->render('BenAssociationBundle:Rooms:multiple.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }
    /**
     * Creates mutiple Room entity.
     * @Secure(roles="ROLE_USER")
     *
     */
    public function createMultipleAction(Request $request)
    {
        $block  = new Rooms();
        $form = $this->createFormBuilder($block)
                ->add('type', 'choice', array('choices' => array('homme' => 'Homme','femme' => 'Femme'),
                    'required' => false,))
                ->add('hotel', null, array('label'  => 'Logement'))
            ->getForm();
        $form->bind($request);

        if($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $hotel = $block->getHotel();
            $type = $block->getType();
            $floors = $request->get('floor');
            if(count($floors) > 0){
                foreach ($floors as $key => $value ) {
                    for ($i=1; $i <= $value['length'] ; $i++) { 
                        $room  = new Rooms();
                        $room_number = ($i<10) ? '0'.$i : $i;
                        $room->setNumber($key.$room_number);
                        $room->setMax($value['capacity']);
                        $room->setFree($value['capacity']);
                        $room->setFloor($key);
                        $room->setType($type);
                        $room->setHotel($hotel);
                        $rooms[] = $room;
                    }
                    $key++;
                }
                foreach ($rooms as $room) {
                    $em->persist($room);
                }
                $em->flush();
            }
            $this->get('session')->getFlashBag()->add('success', "Les chambres ont été ajouté avec succée.");
            return $this->redirect($this->generateUrl('rooms'));
        }

        $this->get('session')->getFlashBag()->add('error', "Il y a des erreurs dans le formulaire soumis !");
        return $this->render('BenAssociationBundle:Rooms:multiple.html.twig', array(
            'entity' => $block,
            'form'   => $form->createView(),
        ));
    }


    /**
     * Displays a form to edit an existing Rooms entity.
     * @Secure(roles="ROLE_MANAGER")
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BenAssociationBundle:Rooms')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Rooms entity.');
        }

        $editForm = $this->createForm(new RoomsType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BenAssociationBundle:Rooms:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Rooms entity.
     * @Secure(roles="ROLE_MANAGER")
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BenAssociationBundle:Rooms')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Rooms entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new RoomsType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('rooms_edit', array('id' => $id)));
        }

        return $this->render('BenAssociationBundle:Rooms:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Rooms entity.
     * @Secure(roles="ROLE_MANAGER")
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('BenAssociationBundle:Rooms')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Rooms entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('rooms'));
    }

    /**
     * export entities to xml or json.
     * @Secure(roles="ROLE_MANAGER")
     *
     */
    public function exportAction($_format)
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('BenAssociationBundle:Rooms')->findAll();
        $entities = array_map(function($entity){
                        return $entity->toArray();
                    }, $entities);

        $encoders = array('json' => new JsonEncoder(), 'xml' => new XmlEncoder());
        $normalizers = array(new GetSetMethodNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $data = $serializer->encode($entities, $_format);

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/'.$_format);

        return $response;
    }
    
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
