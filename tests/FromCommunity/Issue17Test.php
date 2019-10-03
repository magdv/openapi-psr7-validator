<?php

declare(strict_types=1);

namespace OpenAPIValidationTests\FromCommunity;

use GuzzleHttp\Psr7\ServerRequest;
use OpenAPIValidation\PSR7\ValidatorBuilder;
use OpenAPIValidation\PSR7\OperationAddress;

use PHPUnit\Framework\TestCase;

final class Issue17Test extends TestCase
{
    /**
    * @see https://github.com/thephpleague/openapi-psr7-validator/issues/17
    */
    public function testIssue57() : void
    {
        $yaml = /** @lang yaml */
            <<<YAML
openapi: 3.0.0
info:
  title: Product import API
  version: '1.0'
servers:
  - url: 'http://localhost:8000/api/v1'
paths:
  /products.create:
    post:
      requestBody:
        required: true
        content:
          application/json:
            schema:
              type: object
              additionalProperties:
                type: string
      responses:
        '200':
          description: OK
          content:
            application/json:
              schema:
                properties:
                  result: 
                    type: string
YAML;
        
        $validator = (new ValidatorBuilder())->fromYaml($yaml)->getRoutedRequestValidator();
        $psrRequest = new ServerRequest(
                    'POST',
                    'http://localhost:8000/api/v1/products.create',
                    ['Content-Type' => 'application/json'],
                    <<<JSON
{
    "stringOne":"foo",
    "stringTwo":"bar",
    "oneObject":{
        "more":"things"
    }
}
JSON
        );
        
        $address = new OperationAddress('/products.create', 'post');
        $validator->validate($address, $psrRequest);
        
        $this->addToAssertionCount(1);
    }
}