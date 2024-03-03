<?php
/*
 * This file is part of the Automate package.
 *
 * (c) Julien Jacottet <jjacottet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Automate\Context;

use Automate\Model\Server;
use Automate\Session\LocalSession;
use Automate\Session\SessionInterface;

class LocalContext extends AbstractContext
{
    public function connect()
    {
    }

    public function getSession(Server $server): SessionInterface
    {
        return new LocalSession();
    }
}
