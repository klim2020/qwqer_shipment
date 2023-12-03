<?php
namespace Opencart\Catalog\Controller\Extension\Qwqer\Startup;
class Fff extends \Opencart\System\Engine\Controller
{
    public function index(): void
    {
        $this->event->register('view/*/before', new \Opencart\System\Engine\Action('extension/extension_name/startup/file_name.func_name'));
    }
}