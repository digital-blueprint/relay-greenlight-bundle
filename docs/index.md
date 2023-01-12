# Overview

Source: https://github.com/digital-blueprint/dbp-relay-greenlight-bundle

```mermaid
graph TD
    style greenlight_bundle fill:#606096,color:#fff

    subgraph API Gateway
        api(("API"))
        greenlight_bundle("Greenlight Bundle")
        greenlight_connector_bundle("Greenlight Connector Bundle")
    end

    api --> greenlight_bundle
    greenlight_bundle --> greenlight_connector_bundle
```

This bundle provides the backend services required for the greenlight
application. Part of the service is fetching a photo of the user for visual
authentication. This part is not implemented directly but outsourced to a
connector bundle which you have to install separately.

## Installation Requirements

* A SQL database like MySQL, PostgreSQL or similar.
