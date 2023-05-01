# price-calculation
Simple example of Symfony Framework, calculate price form.

# Create .env file with variables
APP_ENV=dev
DATABASE_URL=""

# To start project in development mode
php -S localhost:8000 -t public

# Example
[Demo](http://price-calculation.aksion.me/)

# Input parameters
- product (select);
- tax number (input).

# Output parameters
- Product;
- Price;
- Tax rate;
- Total price.

# Formula of price calculation
totalPrice = productPrice * (1 + taxRate).
