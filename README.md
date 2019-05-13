# LiveTwig

Let's get rid of the SPAs! Here's how to get real-time Twig blocks basically. Bisous. 😘

## Usage

```
composer req sroze/live-twig
```

## Get Mercure Hub running

Following [the Mercure documentation](https://github.com/dunglas/mercure) if you don't have it up and running (or [contact me](mailto:samuel.roze@gmail.com) for managed options). 

TL;DR: Simplest is with Docker:
```
docker run --rm -e CORS_ALLOWED_ORIGINS='http://localhost:8000' -e JWT_KEY='!UnsecureChangeMe!' -e DEMO=1 -e ALLOW_ANONYMOUS=1 -e PUBLISH_ALLOWED_ORIGINS='http://localhost,http://localhost:8000' -p 80:80 dunglas/mercure
```

## Set environment variables

```
# .env
# ...

MERCURE_PUBLISH_URL=http://localhost/hub
MERCURE_TOKEN=[your-token]
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

---

## TODO

- [ ] Add authentication (generate tokens in Symfony - for publish & consume).
