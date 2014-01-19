<?php

use Zend\Permissions\Acl\Role\GenericRole as Role;
use Zend\Permissions\Acl\Resource\GenericResource as Resource;
$guest = new Role('guest');
$admin = new Role('admin');
$acl->addRole($guest);
$acl->addRole($admin, array('guest'));
$resource = new Resource('document');
$acl->addResource($resource);
$acl->addResource('section');//string ok
$acl->allow('admin', 'document', array('publish', 'edit'));

/**
 * roleを追加する前にallowはできません。
 */
//$acl->allow('administrator');

$acl->addRole('administrator');
$acl->allow('administrator');