<?php

namespace Symfony\Component\Live\Twig;

use Twig\Node\Expression\AssignNameExpression;
use Twig\TokenParser\AbstractTokenParser;
use Twig\Token;

class LiveSubscriberTokenParser extends AbstractTokenParser
{
    /**
     * {@inheritdoc}
     */
    public function parse(Token $token)
    {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();

        // {% liveSubscription 'bar' %}
        $name = $this->parser->getExpressionParser()->parseExpression();

        $stream->expect(Token::BLOCK_END_TYPE);

        // {% endLiveSubscription %}
        $body = $this->parser->subparse(array($this, 'decideLiveSubscriberEnd'), true);
        $stream->expect(Token::BLOCK_END_TYPE);

        return new LiveSubscriberNode($name, $body, new AssignNameExpression($this->parser->getVarName(), $token->getLine()), $lineno, $this->getTag());
    }

    public function decideLiveSubscriberEnd(Token $token)
    {
        return $token->test('endLiveSubscription');
    }

    /**
     * {@inheritdoc}
     */
    public function getTag()
    {
        return 'liveSubscription';
    }
}
