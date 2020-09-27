<?php


namespace App\Core\Controller;


interface ControllerInterface
{
    public function createAction();
    public function createPostAction();
    public function editAction($id);
    public function editPostAction();
    public function deleteAction($id);
    public function detailsAction($id);
}