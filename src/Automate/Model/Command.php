<?php

/*
 * This file is part of the Automate package.
 *
 * (c) Julien Jacottet <jjacottet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Automate\Model;

class Command extends Action
{
    /**
     * @param null|string[] $only
     */
    public function __construct(
        private ?string $cmd = null,
        protected ?array $only = null,
    ) {
    }

    public function getCmd(): ?string
    {
        return $this->cmd;
    }

    public function setCmd(string $cmd): self
    {
        $this->cmd = $cmd;

        return $this;
    }
}
