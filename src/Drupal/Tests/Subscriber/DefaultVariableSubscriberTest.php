<?php
/**
 * Created by PhpStorm.
 * User: rbayliss
 * Date: 11/8/17
 * Time: 5:08 PM
 */

namespace LastCall\Mannequin\Drupal\Tests\Subscriber;


use Drupal\Core\Template\Attribute;
use LastCall\Mannequin\Core\Component\ComponentCollection;
use LastCall\Mannequin\Core\Component\Sample;
use LastCall\Mannequin\Core\Event\ComponentEvents;
use LastCall\Mannequin\Core\Event\RenderEvent;
use LastCall\Mannequin\Core\Rendered;
use LastCall\Mannequin\Core\Tests\Subscriber\ComponentSubscriberTestTrait;
use LastCall\Mannequin\Drupal\Component\DrupalTwigComponent;
use LastCall\Mannequin\Drupal\Subscriber\DefaultVariablesSubscriber;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;

class DefaultVariableSubscriberTest extends TestCase
{
    use ComponentSubscriberTestTrait;

    public function getTests() {
        return [
            [
                [],
                [
                    'attributes' => new Attribute(),
                    'title_attributes' => new Attribute(),
                    'content_attributes' => new Attribute(),
                    'title_prefix' => [],
                    'title_suffix' => [],
                    'db_is_active' => true,
                    'is_admin' => false,
                    'logged_in' => false,
                ],
                'Defaults should fill any variable that has not been set.'
            ],
            [
                [
                    'attributes' => new Attribute(['foo' => 'bar']),
                    'title_attributes' => new Attribute(['foo' => 'bar']),
                    'content_attributes' => new Attribute(['foo' => 'bar']),
                    'title_prefix' => ['foo'],
                    'title_suffix' => ['foo'],
                    'db_is_active' => false,
                    'is_admin' => true,
                    'logged_in' => true,
                ],
                [
                    'attributes' => new Attribute(['foo' => 'bar']),
                    'title_attributes' => new Attribute(['foo' => 'bar']),
                    'content_attributes' => new Attribute(['foo' => 'bar']),
                    'title_prefix' => ['foo'],
                    'title_suffix' => ['foo'],
                    'db_is_active' => false,
                    'is_admin' => true,
                    'logged_in' => true,
                ],
                'Defaults should not override existing values.',
            ]
        ];
    }

    /**
     * @dataProvider getTests
     */
    public function testSetsDefaultValues($input, $expected, $message) {
        $collection = $this->prophesize(ComponentCollection::class);
        $component = $this->prophesize(DrupalTwigComponent::class);
        $sample = $this->prophesize(Sample::class);

        $event = new RenderEvent(
            $collection->reveal(),
            $component->reveal(),
            $sample->reveal(),
            new Rendered()
        );
        $event->setVariables($input);

        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber(new DefaultVariablesSubscriber());
        $dispatcher->dispatch(ComponentEvents::PRE_RENDER, $event);
        $this->assertEquals($expected, $event->getVariables(), $message);
    }
}