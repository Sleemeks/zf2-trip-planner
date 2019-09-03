<?php

namespace Beeewithus\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class DateController extends AbstractActionController
{
    public function indexAction()
    {
        $view = new ViewModel();
        $view->setTemplate('beeewithus/index/date123');
        $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\RendererInterface');

        return new ViewModel(array(
            'te' => $renderer->render($view)
        ));
    }
}
