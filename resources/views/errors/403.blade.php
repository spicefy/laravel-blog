<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>403 Forbidden</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="text-center bg-white p-8 rounded-xl shadow-lg max-w-md">
        <h1 class="text-6xl font-bold text-red-500">403</h1>
        <h2 class="text-xl font-semibold mt-4">Access Denied</h2>

        <p class="text-gray-600 mt-2">
            {{ $exception->getMessage() ?: 'You are not allowed to access this page.' }}
        </p>

        <a href="{{ url('/') }}"
           class="mt-6 inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
            Go Back Home
        </a>
    </div>

</body>
</html>