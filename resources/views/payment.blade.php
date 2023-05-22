<!-- Include Stripe.js library -->
<script src="https://js.stripe.com/v3/"></script>

<!-- Payment form -->
<form action="/payment" method="POST">
    @csrf
    <div class="form-group">
        <label for="card-element">
            Credit or debit card
        </label>
        <div id="card-element"></div>
    </div>

    <button type="submit">Pay</button>
</form>

<!-- JavaScript to create a token from card details -->
<script>
    const stripe = Stripe('{{ config('services.stripe.key') }}');
    const elements = stripe.elements();
    const cardElement = elements.create('card');

    cardElement.mount('#card-element');

    const form = document.querySelector('form');

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const { token, error } = await stripe.createToken(cardElement);

        if (error) {
            console.error(error);
        } else {
            // Append the token to the form and submit
            const tokenInput = document.createElement('input');
            tokenInput.setAttribute('type', 'hidden');
            tokenInput.setAttribute('name', 'stripeToken');
            tokenInput.setAttribute('value', token.id);
            form.appendChild(tokenInput);
            form.submit();
        }
    });
</script>
