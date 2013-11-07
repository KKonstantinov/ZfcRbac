<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace ZfcRbac\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfcRbac\Exception;
use ZfcRbac\Role\RoleProviderChain;

/**
 * Factory to create a role provider chain
 */
class RoleProviderChainFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var \ZfcRbac\Options\ModuleOptions $options */
        $options = $serviceLocator->get('ZfcRbac\Options\ModuleOptions');
        $options = $options->getRoleProviders();

        $roleProviders = array();

        foreach ($options as $option) {
            if (!isset($option['type'])) {
                throw new Exception\RuntimeException('Missing "type" option for ZfcRbac role provider');
            }

            // @TODO: ZF2 service manager does not support options, so we need to create a plugin manager
            // to be able to do this:
            $providerOptions = isset($options['options']) ? $options['options'] : array();
            $roleProviders[] = $serviceLocator->get($option['type'], $providerOptions);
        }

        return new RoleProviderChain($roleProviders);
    }
}
