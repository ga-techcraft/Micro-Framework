<?php

return [
  'global'=>[
      \Middleware\SessionsSetupMiddleware::class,
      \Middleware\MiddlewareA::class,
      \Middleware\MiddlewareB::class,
      \Middleware\MiddlewareC::class,
  ]
];