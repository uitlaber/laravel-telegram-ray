---

# Laravel Telegram Ray

A simple and convenient Laravel package that adds a `ray()` helper function for sending debug information directly to your Telegram chat.
It’s a great alternative to Xdebug, `dd()`, or `dump()`, especially when working on a remote server or when you want to receive instant notifications.

---

## ## Features

* **Easy to use:** Familiar `ray()` function available anywhere in your Laravel application.
* **Any data:** Send strings, arrays, objects, collections, Eloquent models, and even exceptions.
* **Full context:** Each message automatically includes:

  * Project name
  * Relative file path and line number where the function was called
  * Full request URL, including all GET parameters
* **Flexible configuration:** All settings are handled in the `.env` file.
* **Easily disabled:** You can enable or disable the function entirely with one `.env` variable, without changing your code.

---

## ## Installation

1. Install the package via Composer:

   ```bash
   composer require uitlaber/laravel-telegram-ray
   ```

2. Publish the configuration file:

   ```bash
   php artisan vendor:publish --provider="Uitlaber\LaravelTelegramRay\TelegramRayServiceProvider"
   ```

   This will create the file `config/telegram-ray.php`.

---

## ## Configuration

Before using the package, you need to obtain your **bot token** and **chat ID** from Telegram.

1. **Bot token:** Create a new bot using **@BotFather** in Telegram and copy the API token it provides.
2. **Chat ID:** Open the **@userinfobot** in Telegram, start it, and it will send you your personal chat ID.

Now add these values and the `ENABLE_RAY_TELEGRAM` toggle to your `.env` file:

```dotenv
# .env

# Enables (true) or disables (false) sending messages
ENABLE_RAY_TELEGRAM=true

# Your bot token from @BotFather
TELEGRAM_BOT_TOKEN="YOUR:BOT_TOKEN"

# Your chat ID from @userinfobot
TELEGRAM_CHAT_ID="YOUR_CHAT_ID"
```

---

## ## Usage

You can call the `ray()` function anywhere in your Laravel application —
in routes, controllers, models, services, or even inside Blade templates (within `@php ... @endphp`).

#### **Examples:**

**Send a simple message:**

```php
Route::get('/', function () {
    ray('User visited the homepage.');
    return view('welcome');
});
```

**Send an array or object:**

```php
use App\Models\User;

$user = User::find(1);
ray($user->toArray());
```

**Send an exception:**

```php
try {
    // Code that might throw an error
    throw new \Exception('Something went wrong!');
} catch (\Exception $e) {
    ray($e);
}
```

#### **Example in Blade:**

```blade
@php
    $data = ['product_id' => 123, 'status' => 'pending'];
    ray($data);
@endphp

<h1>Product Card</h1>
```

---

## ## Example Message in Telegram

Here’s how a debug message will appear in your Telegram chat:

> 🚀 **Laravel**
> 📄 `routes/web.php:6`
> 📍 `[GET] http://sandbox.test/?data=1`
>
> ```json
> {
>     "id": 1,
>     "name": "Damir",
>     "email": "damir@example.com"
> }
> ```

---

## ## License

This package is open-source software licensed under the [MIT license](LICENSE.md).

---
