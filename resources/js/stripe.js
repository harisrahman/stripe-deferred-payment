import axios from "axios";
import { setBtnLoading } from "./button";

const stripe = window.Stripe(import.meta.env.VITE_STRIPE_PUBLIC_KEY);

const options = {
    mode: "subscription",
    amount: 1099, // $10.99
    currency: "usd",
    // Fully customizable with appearance API.
    appearance: {
        /*...*/
    },
    /**
     * A list of payment method types to render. You can omit this
     * attribute to manage your payment methods from the Stripe
     * Dashboard.
     *
     * Available options : card, us_bank_account, amazon_pay, link etc.
     *
     * https://docs.stripe.com/js/elements_object/create_without_intent#stripe_elements_no_intent-options-paymentMethodTypes
     */
    paymentMethodTypes: ["card"],
};

// Set up Stripe.js and Elements to use in checkout form
const elements = stripe.elements(options);

// Create and mount the Payment Element
const paymentElementOptions = { layout: "accordion" };
const paymentElement = elements.create("payment", paymentElementOptions);
paymentElement.mount("#payment-element");

const form = document.getElementById("payment-form");
const submitBtn = document.getElementById("submit");

const handleError = (error) => {
    const messageContainer = document.querySelector("#error-message");
    messageContainer.textContent = error.message;
    submitBtn.disabled = false;
};

form.addEventListener("submit", async (e) => {
    // We don't want to let default form submission happen here,
    // which would refresh the page.
    e.preventDefault();

    // Prevent multiple form submissions
    if (submitBtn.disabled) {
        return;
    }

    // Trigger form validation and wallet collection
    const { error: submitError } = await elements.submit();
    if (submitError) {
        handleError(submitError);
        return;
    }

    setBtnLoading(submitBtn, true);

    const formData = new FormData(form);

    // Create the subscription
    const response = await axios.post("/api/stripe/subscription", formData);
    const { order_id, type, client_secret } = response.data;

    const confirmIntent =
        type === "setup" ? stripe.confirmSetup : stripe.confirmPayment;

    const returnUrl =
        window.location.href +
        `?order_id=${order_id}&clientSecret=${client_secret}`;

    // Confirm the Intent using the details collected by the Payment Element
    const intent = await confirmIntent({
        elements,
        clientSecret: client_secret,
        confirmParams: {
            // Return back to same
            return_url: returnUrl,
        },
        // change to "always" if you want to redirect to return_url always
        // https://docs.stripe.com/js/payment_intents/confirm_payment#confirm_payment_intent-options-redirect
        redirect: "if_required",
    });

    const { paymentIntent, error } = intent;

    if (error) {
        setBtnLoading(submitBtn, false);

        // This point is only reached if there's an immediate error when confirming the Intent.
        // Show the error to your customer (for example, "payment details incomplete").
        handleError(error);
    } else {
        // Your customer is redirected to your `return_url`. For some payment
        // methods like iDEAL, your customer is redirected to an intermediate
        // site first to authorize the payment, then redirected to the `return_url`.
        const response = await axios.post("/api/stripe/subscription/complete", {
            payment_intent_id: paymentIntent.id,
            order_id,
        });

        setBtnLoading(submitBtn, false);

        const { token, refresh_token } = response.data;

        // Set auth tokens and redirect to logged in page
        window.localStorage.setItem("token", token);
        window.localStorage.setItem("refresh_token", refresh_token);
    }
});
