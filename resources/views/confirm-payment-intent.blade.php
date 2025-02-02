<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Confirm Payment Intent</title>
</head>

<body>
    <div class="mt-4">
        <form id="intent-form" class="max-w-lg mx-auto">
            <h1 class="text-3xl font-bold text-orange-500 mb-4">Confirm Payment Intent</h1>
            <div class="mb-5">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Payment Intent Id
                        <input type="text" name="payment_intent_client_secret"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            maxlength="255" required />
                    </label>
                    <br>
                </div>
                <div id="payment-element">
                    <!-- Elements will create form elements here -->
                </div>
                <br />
                <button id="submit"
                    class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none">
                    Submit
                    <span
                        class="hidden animate-spin size-4 border-[3px] border-current border-t-transparent text-white rounded-full"
                        role="status" aria-label="loading"></span>
                </button>
                <div id="response-message">
                    <!-- Display error or success message to your customers here -->
                </div>
            </div>
        </form>
    </div>

    <script src="https://js.stripe.com/v3/"></script>
    @vite(['resources/css/app.scss', 'resources/js/confirm-intent.js'])
</body>

</html>
