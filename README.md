# LiveTwig

Let's get rid of the SPAs! Here's how to get real-time Twig blocks basically. Bisous. 😘

## Setup

0. Install the dependency
       
    composer req sroze/live-twig

1. Get Mercure Hub running. You can have a look to [the Mercure documentation](https://mercure.rocks/docs/hub/install).
  Simplest is with Docker:

    docker run --rm -e CORS_ALLOWED_ORIGINS='https://127.0.0.1:8000' -e JWT_KEY='!ChangeMe!' -e DEMO=1 -e ALLOW_ANONYMOUS=1 -e PUBLISH_ALLOWED_ORIGINS='http://localhost,http://localhost:8000' -p 80:80 dunglas/mercure

2. Get your Mercure JWT token. If you are using the default demo `JWT_KEY`, you can get the token from
   [your running hub's homepage.](http://localhost/).

3. Set environment variables

    # .env
    # ...
    
    MERCURE_PUBLISH_URL=http://localhost/.well-known/mercure
    MERCURE_JWT_TOKEN=[your-token]

4. (While the Flex recipe is not done) Regisyer the bundle:

   ```php
   // config/bundles.php
   return [
       // ...
       Symfony\Bundle\LiveTwigBundle\LiveTwigBundle::class => ['all' => true],
   ];
   ```

   Create the following configuration file:

   ```yaml
   # config/packages/live_twig.yaml
   live_twig:
       mercure_public_url: "%env(MERCURE_PUBLISH_URL)%"
   ```

## Use it!

Anywhere in your Twig templates, render a live block:

```twig
{{
    render_live(
        controller('App\\Controller\\YourController::yourAction', { some: 'parameter' }),
        {'topics': ['foo', 'bar-' ~ baz]}
    )
}}
```

When the data contained in this block change, publish an empty Mercure update:

```php
<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\Publisher;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;

class LiveController extends AbstractController
{
    /**
     * @Route("/foo")
     */
    public function index(Publisher $publisher)
    {
        // ...
        $publisher(new Update(['foo', 'bar-'.$bar]));
        // ...
    }
}
```

When the browser will receive the signal, it will send a GET request to retrieve the new version of the block.
Alternatively, you can also pass the data to display as the content of the update. In this case no extra HTTP request is triggered:

```php
// ...
$publisher(new Update(['foo', 'bar-'.$bar], 'the updated content of the block'));
```
