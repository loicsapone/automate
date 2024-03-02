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

interface ContextInterface
{
    /**
     * connect servers.
     */
    public function connect();

    /**
     * Get serveur's Session.
     */
    public function getSession(Server $server);

    /**
     * Get GitRef.
     *
     * @return string
     */
    public function getGitRef();

    /**
     * Get Project.
     *
     * @return Project
     */
    public function getProject();

    /**
     * Get Platform.
     *
     * @return Platform
     */
    public function getPlatform();

    /**
     * Get Logger.
     *
     * @return LoggerInterface
     */
    public function getLogger();

    /**
     * Is Deployed.
     *
     * @return bool
     */
    public function isDeployed();

    /**
     * @param bool $isDeployed
     *
     * @return Context
     */
    public function setDeployed($isDeployed);

    /**
     * Is Force.
     *
     * @return bool
     */
    public function isForce();

    /**
     * @param bool $force
     *
     * @return Context
     */
    public function setForce($force);

    /**
     * Get a release ID.
     *
     * @return string
     */
    public function getReleaseId();

    /**
     * Execute e command.
     *
     * @param string     $command
     * @param bool       $verbose
     * @param null|array $specificServers
     * @param bool       $addWorkingDir
     *
     * @return mixed
     */
    public function run($command, $verbose = false, $specificServers = null, $addWorkingDir = true);

    /**
     * Run on server.
     *
     * @param string $command
     * @param bool   $addWorkingDir
     * @param bool   $verbose
     *
     * @return string
     */
    public function doRun(Server $server, $command, $addWorkingDir = true, $verbose = false);

    /**
     * Get release path.
     *
     * @return string
     */
    public function getReleasePath(Server $server);

    /**
     * Get releases path.
     *
     * @return string
     */
    public function getReleasesPath(Server $server);

    /**
     * Get shared path.
     *
     * @return string
     */
    public function getSharedPath(Server $server);

    /**
     * Get current path.
     *
     * @return string
     */
    public function getCurrentPath(Server $server);
}
