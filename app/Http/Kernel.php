@@ .. @@
     protected $routeMiddleware = [
         'auth' => \App\Http\Middleware\Authenticate::class,
         'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
+        'auth.jwt' => \Tymon\JWTAuth\Http\Middleware\Authenticate::class,
         'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
         'can' => \Illuminate\Auth\Middleware\Authorize::class,
         'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
         'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
         'signed' => \Illuminate\Http\Middleware\ValidateSignature::class,
         'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
         'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
     ];