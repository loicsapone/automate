<?php

/*
 * This file is part of the Automate package.
 *
 * (c) Julien Jacottet <jjacottet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Automate\Tests;

use Automate\Loader;
use Automate\Model\Action;
use Automate\Model\Command;
use Automate\Model\Project;
use Automate\Model\Server;
use Automate\Model\Upload;
use PHPUnit\Framework\TestCase;

class LoaderTest extends TestCase
{
    public function testLoader(): void
    {
        $loader = new Loader();

        $project = $loader->load(__DIR__.'/../fixtures/config.yml');

        $this->assertInstanceOf(Project::class, $project);
        $this->assertEquals('git@github.com:julienj/symfony-demo.git', $project->getRepository());
        $this->assertEquals(['app/data'], $project->getSharedFolders());
        $this->assertEquals(['app/config/parameters.yml'], $project->getSharedFiles());

        $this->assertcount(1, $project->getPreDeploy());
        $preDeploy = current($project->getPreDeploy());
        $this->assertInstanceOf(Command::class, $preDeploy);
        $this->assertEquals('php -v', $preDeploy->getCmd());

        foreach ($project->getOnDeploy() as $onDeploy) {
            $this->assertInstanceOf(Command::class, $onDeploy);
        }

        $this->assertEquals('composer install', $project->getOnDeploy()[0]->getCmd());
        $this->assertEquals('setfacl -R -m u:www-data:rwX -m u:`whoami`:rwX var', $project->getOnDeploy()[1]->getCmd());
        $this->assertEquals('setfacl -dR -m u:www-data:rwX -m u:`whoami`:rwX var', $project->getOnDeploy()[2]->getCmd());

        foreach ($project->getPostDeploy() as $postDeploy) {
            $this->assertInstanceOf(Action::class, $postDeploy);
        }

        $this->assertInstanceOf(Command::class, $project->getPostDeploy()[0]);
        $this->assertEquals('php bin/console doctrine:cache:clear-metadata', $project->getPostDeploy()[0]->getCmd());
        $this->assertEquals([], $project->getPostDeploy()[0]->getOnly());

        $this->assertInstanceOf(Command::class, $project->getPostDeploy()[1]);
        $this->assertEquals('php bin/console doctrine:schema:update --force', $project->getPostDeploy()[1]->getCmd());
        $this->assertEquals(['eddv-exemple-front-01'], $project->getPostDeploy()[1]->getOnly());

        $this->assertInstanceOf(Command::class, $project->getPostDeploy()[2]);
        $this->assertEquals('php bin/console doctrine:cache:clear-result', $project->getPostDeploy()[2]->getCmd());
        $this->assertEquals([], $project->getPostDeploy()[2]->getOnly());

        $this->assertInstanceOf(Command::class, $project->getPostDeploy()[3]);
        $this->assertEquals('php bin/console messenger:consume', $project->getPostDeploy()[3]->getCmd());
        $this->assertEquals(['eddv-exemple-front-01', 'dddv-exemple-front-01'], $project->getPostDeploy()[3]->getOnly());

        $this->assertInstanceOf(Upload::class, $project->getPostDeploy()[4]);
        $this->assertEquals('public/build', $project->getPostDeploy()[4]->getPath());
        $this->assertEquals(['vendor'], $project->getPostDeploy()[4]->getExclude());

        $this->assertCount(2, $project->getPlatforms());

        $platform = $project->getPlatform('development');
        $this->assertEquals('development', $platform->getName());
        $this->assertEquals('master', $platform->getDefaultBranch());
        $this->assertEquals(3, $platform->getMaxReleases());
        $this->assertCount(1, $platform->getServers());

        /** @var Server $server */
        $server = current($platform->getServers());

        $this->assertEquals('dddv-exemple-front-01', $server->getName());
        $this->assertEquals('192.168.1.18', $server->getHost());
        $this->assertEquals('root', $server->getUser());
        $this->assertEquals('%dev_password%', $server->getPassword());
        $this->assertEquals('/home/wwwroot/automate/demo', $server->getPath());
    }

    public function testSharedPathLoader(): void
    {
        $loader = new Loader();

        $project = $loader->load(__DIR__.'/../fixtures/simpleWithSharedPath.yml');

        $platform = $project->getPlatform('development');

        /** @var Server $server */
        $server = current($platform->getServers());

        $this->assertEquals('/home/wwwroot/shared', $server->getSharedPath());
    }
}
