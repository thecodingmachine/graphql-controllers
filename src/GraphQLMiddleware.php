<?php
namespace TheCodingMachine\GraphQL\Controllers;

use GraphQL\GraphQL;
use Youshido\GraphQL\Schema\AbstractSchema;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Youshido\GraphQL\Execution\Processor;
use Zend\Diactoros\Response\JsonResponse;

class GraphQLMiddleware implements \Interop\Http\ServerMiddleware\MiddlewareInterface
{
    /**
     * @var string The graphql uri path to match against
     */
    private $graphqlUri;

    /**
     * @var array The graphql headers
     */
    private $graphql_headers = [
        "application/graphql"
    ];

    /**
     * @var array Allowed method for a graphql request, default GET, POST
     */
    private $allowed_methods = [
        "GET", "POST", "OPTIONS"
    ];

    /**
     * @var Processor
     */
    private $schema;
    
    /**
     * @var string The allowed origins for CORS requests
     */
    private $allowedOrigins;

    /**
     * GraphQLMiddleware constructor.
     *
     * @param AbstractSchema    $schema
     * @param string    $graphqlUri
     */
    public function __construct(AbstractSchema $schema, $graphqlUri = '/graphql', $rootUrl = null, $allowedOrigins = '')
    {
        $this->schema = $schema;
        $this->graphqlUri = rtrim($rootUrl, '/').'/'. ltrim($graphqlUri, '/');
        $this->allowedOrigins = $allowedOrigins;
    }

    /**
     * Process an incoming server request and return a response, optionally delegating
     * to the next middleware component to create the response.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Interop\Http\ServerMiddleware\DelegateInterface $delegate
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(\Psr\Http\Message\ServerRequestInterface $request, \Interop\Http\ServerMiddleware\DelegateInterface $delegate)
    {
        if (!$this->isGraphQLRequest($request)) {
            return $delegate->process($request);
        }

        if (!in_array($request->getMethod(), $this->allowed_methods, true)) {
            return new JsonResponse([
                "Method not allowed. Allowed methods are " . implode(", ", $this->allowed_methods)
            ], 405);
        }

        if($request->getMethod() == "OPTIONS"){ // CORS graphQL fetch Requests
            return new JsonResponse("OK", 200, [
                "Access-Control-Allow-Origin" => $this->allowedOrigins,
                "Access-Control-Allow-Headers" => "Content-Type, Authorization, Content-Length, X-Requested-With",
                "Access-Control-Allow-Method" => "GET, POST"
            ]);
        } else {
            list($query, $variables) = $this->getPayload($request);
        }


        $processor = new Processor($this->schema);
        $processor->processPayload($query, $variables);
        $res = $processor->getResponseData();
        return new JsonResponse($res);

        /*$res = GraphQL::execute($this->schema->toGraphQLSchema(), $query, null, null, $variables, null);

        return new JsonResponse($res);*/
    }

    private function isGraphQLRequest(ServerRequestInterface $request)
    {
        return $this->hasUri($request) || $this->hasGraphQLHeader($request);
    }

    private function hasUri(ServerRequestInterface $request)
    {
        return  $this->graphqlUri === $request->getUri()->getPath();
    }

    private function hasGraphQLHeader(ServerRequestInterface $request)
    {
        if (!$request->hasHeader('content-type')) {
            return false;
        }

        $request_headers = array_map(function ($header) {
            return trim($header);
        }, explode(",", $request->getHeaderLine("content-type")));

        foreach ($this->graphql_headers as $allowed_header) {
            if (in_array($allowed_header, $request_headers)) {
                return true;
            }
        }

        return  false;
    }

    private function getPayload(ServerRequestInterface $request)
    {
        $method = $request->getMethod();

        switch ($method) {
            case "GET":
                return $this->fromGet($request);
            case "POST":
                return $this->fromPost($request);
            default:
                throw new \RuntimeException('Unexpected request type. Only support GET and POST.');

        }
    }

    private function fromGet(ServerRequestInterface $request)
    {
        $params = $request->getQueryParams();

        $query = isset($params['query']) ? $params['query'] : null;
        $variables = isset($params['variables']) ? $params['variables'] : [];

        $variables = is_string($variables) ? json_decode($variables, true) ?: [] : [];

        return [$query, $variables];
    }

    private function fromPost(ServerRequestInterface $request)
    {
        $content = $request->getBody()->getContents();

        if (empty($content)) {
            $params = $request->getParsedBody();
        } else {
            $params = json_decode($content, true);
        }

        $query = $variables = null;

        if (!empty($params)) {
            if ($this->hasGraphQLHeader($request)) {
                $query = $content;
            } else {
                if ($params) {
                    $query = isset($params['query']) ? $params['query'] : $query;
                    if (isset($params['variables'])) {
                        if (is_string($params['variables'])) {
                            $variables = json_decode($params['variables'], true) ?: $variables;
                        } else {
                            $variables = $params['variables'];
                        }
                        $variables = is_array($variables) ? $variables : [];
                    }
                }
            }
        }
        return [$query, $variables];
    }
}
