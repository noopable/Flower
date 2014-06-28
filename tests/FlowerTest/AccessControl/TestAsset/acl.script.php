<?php
namespace Zend\Permissions\Acl;
$guest  = new Role\GenericRole('guest');
/** @var Zend\Permissions\Acl\Acl $acl */
$acl->addRole($guest);
$editor = new Role\GenericRole('editor');
$acl->addRole($editor, array($guest));

if (isset($foo) && is_string($foo)) {
    $acl->addRole($foo);
}