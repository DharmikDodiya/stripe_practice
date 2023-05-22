<form action="{{route('make.payment')}}" method="post">
    @csrf
    <script
    src="https://checkout.stripe.com/checkout.js"
    class="stripe-button"
    data-key="pk_test_51N9PI1SA4SjjlNffG6YCeGvS41kkScNZDULpH661o0MymvTW4zTsLuPvMFZd5Az1oz4QsVq2gkYF5tVjZOaOFYYn000697qUx3"
    data-name="T-shirt"
    data-description="Comfortable cotton t-shirt"
    data-amount="2000"
    data-image="https://www.webappfix.com/storage/app/public/site-setting/SRBx2hTgEOaHdozWVR3hgPb3LTdEw9NwajD05FL2.png"
    data-currency="usd"
    data-label="Make Payment">
  </script>
</form>
