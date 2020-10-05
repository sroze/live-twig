<?php

namespace Symfony\Component\LiveTwig\Fragment;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\HttpKernel\Fragment\FragmentRendererInterface;
use Symfony\Component\HttpKernel\Fragment\RoutableFragmentRenderer;
use Symfony\Component\HttpKernel\UriSigner;
use Symfony\Component\LiveTwig\Event\RenderedLiveFragment;

class LiveTwigFragmentRenderer extends RoutableFragmentRenderer
{
    private $inlineRenderer;
    private $signer;
    private $eventDispatcher;
    private $hubUrl;

    public function __construct(
        FragmentRendererInterface $inlineRenderer,
        UriSigner $signer,
        EventDispatcherInterface $eventDispatcher,
        string $hubUrl
    ) {
        $this->inlineRenderer = $inlineRenderer;
        $this->signer = $signer;
        $this->eventDispatcher = $eventDispatcher;
        $this->hubUrl = $hubUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function render($uri, Request $request, array $options = [])
    {
        if (!$uri instanceof ControllerReference) {
            throw new \InvalidArgumentException('Live renderer can only be used with a controller reference.');
        }

        if (!isset($options['topics']) || !\is_array($options['topics'])) {
            throw new \InvalidArgumentException('The `topics` option must be set and must be an array of strings.');
        }

        $fragmentUri = $this->generateSignedFragmentUri($uri, $request);
        $fragmentId = sha1($fragmentUri);

        $response = $this->inlineRenderer->render($uri, $request, $options);

        // Let the subscribers know about the fragment behind rendered
        $this->eventDispatcher->dispatch(new RenderedLiveFragment(
            $fragmentId,
            $fragmentUri,
            $options['topics']
        ));

        $content =
            '<symfony-live-twig id="'.$fragmentId.'" url="'.$fragmentUri.'" topics="'.implode(',', $options['topics']).'" hub="'.$this->hubUrl.'">'.
            $response->getContent().
            '</symfony-live-twig>'
        ;

        $response->setContent($content);

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'live';
    }
    private function generateSignedFragmentUri($uri, Request $request): string
    {
        // we need to sign the absolute URI, but want to return the path only.
        $fragmentUri = $this->signer->sign($this->generateFragmentUri($uri, $request, true));

        return substr($fragmentUri, \strlen($request->getSchemeAndHttpHost()));
    }
}
