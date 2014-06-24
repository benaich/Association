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
        $keyword = $request->get('keyword');
        $number = $request->get('number');
        $floor = $request->get('floor');
        $status = $request->get('filter');
        $bedNumber = $request->get('bednumber');
        $gender = $request->get('gender');

        $template='BenAssociationBundle:Rooms:ajax_list.html.twig';
        $entities = $em->getRepository('BenAssociationBundle:Rooms')->findSome($perPage, $page, $keyword, $number, $floor, $bedNumber, $gender, $status);
        return $this->render($template, array(
                    'entities' => $entities,
                    'nombreParPage' => $perPage,
                    'nombrePage' => ceil(count($entities) / $perPage),
                    'page' => $page));
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
     * @Secure(roles="ROLE_MANAGER")
     */
    public function enableAction(Request $request, $etat)
    {
        $users = $request->get('entities');
        $em = $this->getDoctrine()->getManager();
        $etat = ($etat==1)? 'open' : 'closed';
        foreach( $users as $id){
            $entity = $em->getRepository('BenAssociationBundle:Rooms')->find($id);
            $entity->setStatus($etat);
            $em->persist($entity);
        }
        $em->flush();
        return new Response('1');
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
