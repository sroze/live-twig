# LiveTwig

Let's get rid of the SPAs! Here's how to get real-time Twig blocks basically. Bisous. 😘

## Setup

0. Install the dependency
       
    ```
    composer req sroze/live-twig
    ```

1. Get Mercure Hub running. You can have a look to [the Mercure documentation](https://github.com/dunglas/mercure).
  Simplest is with Docker:
    ```
    docker run --rm -e CORS_ALLOWED_ORIGINS='http://localhost:8000' -e JWT_KEY='!ChangeMe!' -e DEMO=1 -e ALLOW_ANONYMOUS=1 -e PUBLISH_ALLOWED_ORIGINS='http://localhost,http://localhost:8000' -p 80:80 dunglas/mercure
    ```
   
2. Get your Mercure JWT token. If you are using the default demo `JWT_KEY`, you can get the token from
   [your running hub's homepage.](http://localhost/).

3. Set environment variables
    ```
    # .env
    # ...
    
    MERCURE_PUBLISH_URL=http://localhost/.well-known/mercure
    MERCURE_JWT_TOKEN=[your-token]
    ```

4. (While the Flex recipe is not done) Create the following configuration file:
   ```yaml
   # config/packages/live_twig.yaml
   live_twig:
       mercure_publisher: "Symfony\\Component\\Mercure\\PublisherInterface"
       mercure_public_url: "%env(MERCURE_PUBLISH_URL)%"
   ```

## Use it!

```twig
{{
    render_live(
        controller('App\\Controller\\YourController::yourAction', { some: 'parameter' }),
        {'tags': ['foo', 'bar-' ~ baz]}
    )
}}
```
