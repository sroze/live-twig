<?php

namespace Symfony\Component\Live\Twig;

use Twig\Compiler;
use Twig\Node\Expression\AssignNameExpression;
use Twig\Node\Node;

class LiveSubscriberNode extends Node
{
    public function __construct(Node $name, Node $body, AssignNameExpression $var, int $lineno = 0, string $tag = null)
    {
        parent::__construct(array(
            'body' => $body,
            'name' => $name,
            'var' => $var
        ), array(), $lineno, $tag);
    }

    public function compile(Compiler $compiler)
    {
        // TODO: Probably need to be something else that is predictable
        // (i.e. identifier would remain the after cache clear)
        $identifier = uniqid();

        $compiler
            ->addDebugInfo($this)
            ->write('')
/*            ->subcompile($this->getNode('var'))
            ->raw(' = ')
            ->subcompile($this->getNode('name'))
            ->write(";\n")
*/
            ->write("\$this->env->getExtension('Symfony\Component\Live\Twig\LiveExtension')->registerSubscription(")
            ->subcompile($this->getNode('name'))
            ->raw(", '$identifier');\n")

            // TODO: Add something on the HTML side as well.
            // --> Identify HTML element based on comments?
            // --> Isn't it easier to create a `div` with an identifier for now?
            ->raw("echo '<div id=\"live-$identifier\">';\n")
            ->subcompile($this->getNode('body'))
            ->raw("echo '</div>';\n")

/*            ->write("\$this->env->getExtension('Symfony\Bridge\Twig\Extension\StopwatchExtension')->getStopwatch()->stop(")
            ->subcompile($this->getNode('var'))
            ->raw(");\n")*/
        ;
    }
}
