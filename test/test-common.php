<?php
/**
 * =============================================================================
 * Copyright (c) 2012, Philip Graham
 * All rights reserved.
 *
 * This file is part of php-annotations and is licensed by the Copyright holder
 * under the 3-clause BSD License.  The full text of the license can be found in
 * the LICENSE.txt file included in the root directory of this distribution or
 * at the link below.
 * =============================================================================
 *
 * This file sets up the environment for running tests.
 *
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

$loader = require __DIR__ . '/../vendor/autoload.php';
$loader->addPsr4('zpt\\anno\\', __DIR__ . '/../zpt/anno');
