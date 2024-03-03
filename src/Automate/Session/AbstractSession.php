<?php
/*
 * This file is part of the Automate package.
 *
 * (c) Julien Jacottet <jjacottet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Automate\Session;

abstract class AbstractSession implements SessionInterface
{
    abstract public function run(string $command): string;

    public function mkdir(string $path, bool $recursive = false): void
    {
        $command = sprintf('mkdir%s %s', $recursive ? ' -p' : '', $path);

        $this->run($command);
    }

    public function mv(string $from, string $to): void
    {
        if (!$this->exists(dirname($to))) {
            $this->run(sprintf('mkdir -p %s', dirname($to)));
        }

        $this->run(sprintf('mv %s %s', $from, $to));
    }

    public function rm(string $path, bool $recursive = false): void
    {
        $this->run(sprintf('rm%s %s', $recursive ? ' -R' : '', $path));
    }

    public function exists(string $path): bool
    {
        if ('Y' === trim((string) $this->run(sprintf('if test -d "%s"; then echo "Y";fi', $path)))) {
            return true;
        }

        return 'Y' === trim((string) $this->run(sprintf('if test -f "%s"; then echo "Y";fi', $path)));
    }

    public function symlink(string $target, string $link): void
    {
        $this->run(sprintf('ln -sfn %s %s', $target, $link));
    }

    public function touch(string $path): void
    {
        $this->run(sprintf('mkdir -p %s', dirname($path)));
        $this->run(sprintf('touch %s', $path));
    }

    public function listDirectory(string $path): array
    {
        $rs = (string) $this->run(sprintf('find %s -maxdepth 1 -mindepth 1 -type d', $path));

        return explode("\n", trim($rs));
    }
}
