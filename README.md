- وب سرویس احراز هویت جیبیت برای لاراول شامل استعلام بانکی، هویتی و خدماتی، صحت‌سنجی و اصالت داده‌های بانکی و اطلاعات شخصی افراد توسط تامین‌کنندگان متعدد و با بالاترین درجه اطمینان مورد بررسی قرار گرفته، تایید و یا رد می‌شود.


## Install
```
composer require codemenco/inquiry
```
add this codes to `config/app.php`

```
\Codemenco\Inquiry\InquiryServiceProvider::class
```

```
'Inquiry' => \Codemenco\Inquiry\InquiryFacade::class,
```
```
php artisan vendor publish
```
and edit ``config/inquiry.php``

## Use

### Examples

#### Get iban from cad number
```
Inquiry::cardToSheba(['number' => '6104.......','iban' => true])
```
