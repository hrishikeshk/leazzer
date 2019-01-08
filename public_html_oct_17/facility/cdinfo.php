<?php
$GError="";
include('header.php');

if(isset($_POST['stripeToken'])){
  echo ('Successfully received token: '.$_POST['stripeToken']);
  header("Location: dashboard.php");
}

?>
<!--inner block start here-->
<div class="inner-block">
    <div class="blank">
    	<h2>Credit Card Details</h2>
    	<div class="blankpage-main">
    		<center>
    			<h4>Credit Card Form</h4>
    			<hr>
					<?php
					if($GError!=""){
						echo "<div class=\"alert alert-info\" role=\"alert\">".$GError."</div>";
					}
					?>
					<center><img src="../images/emsurvey2.png" /><br /></center>
          
          <script src="https://js.stripe.com/v3/"></script>
          <!-- script src="js/stripehelper.js"></script -->
          <script>// Create a Stripe client.
          $(document).ready(function(){
            var stripe = Stripe('pk_test_TYooMQauvdEDq54NiTphI7jx');

            // Create an instance of Elements.
            var elements = stripe.elements();

            // Custom styling can be passed to options when creating an Element.
            // (Note that this demo uses a wider set of styles than the guide below.)
            var style = {
                          base: {
                              color: '#32325d',
                              lineHeight: '18px',
                              fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                              fontSmoothing: 'antialiased',
                              fontSize: '16px',
                              '::placeholder': {
                                                color: '#aab7c4'
                              }
                          },
                          invalid: {
                                    color: '#fa755a',
                                    iconColor: '#fa755a'
                          }
            };

            // Create an instance of the card Element.
            var card = elements.create('card', {style: style});

            // Add an instance of the card Element into the `card-element` <div>.
            card.mount('#card-element');

            // Handle real-time validation errors from the card Element.
            card.addEventListener('change', function(event) {
            var displayError = document.getElementById('card-errors');
            if (event.error) {
                  displayError.textContent = event.error.message;
            } else {
                  displayError.textContent = '';
            }
        });

        // Handle form submission.
        var form = document.getElementById('payment-form');
        form.addEventListener('submit', function(event) {
        event.preventDefault();

        stripe.createToken(card).then(function(result) {
        if (result.error) {
          // Inform the user if there was an error.
          var errorElement = document.getElementById('card-errors');
          errorElement.textContent = result.error.message;
        } else {
          // Send the token to your server.
          stripeTokenHandler(result.token);
        }
     });
   });

  // Submit the form with the token ID.
  function stripeTokenHandler(token) {
    // Insert the token ID into the form so it gets submitted to the server
    var form = document.getElementById('payment-form');
    var hiddenInput = document.createElement('input');
    hiddenInput.setAttribute('type', 'hidden');
    hiddenInput.setAttribute('name', 'stripeToken');
    hiddenInput.setAttribute('value', token.id);
    form.appendChild(hiddenInput);

    // Submit the form
    form.submit();
  }
});

</script>
          <!-- link rel="stylesheet" type="text/css" href="css/stripehelper.css" / -->
<style>
/**
 * The CSS shown here will not be introduced in the Quickstart guide, but shows
 * how you can use CSS to style your Element's container.
 */
.StripeElement {
  background-color: white;
  height: 40px;
  padding: 10px 12px;
  border-radius: 4px;
  border: 1px solid transparent;
  box-shadow: 0 1px 3px 0 #e6ebf1;
  -webkit-transition: box-shadow 150ms ease;
  transition: box-shadow 150ms ease;
}

.StripeElement--focus {
  box-shadow: 0 1px 3px 0 #cfd7df;
}

.StripeElement--invalid {
  border-color: #fa755a;
}

.StripeElement--webkit-autofill {
  background-color: #fefde5 !important;
}

</style>          
          <form action="/facility/cdinfo.php" method="post" id="payment-form">

            <div class="form-row">
            <label for="card-element">
              Credit Card Number
            </label>
            <div id="card-element">
              <!-- A Stripe Element will be inserted here. -->
              
            </div>
            <img src="../images/shield.jpg" height="7%" width="7%" />
            <!-- Used to display form errors. -->
            <div id="card-errors" role="alert"></div>
            </div>

            <button>Submit</button>
          </form>
          <br /><h4>Note: No charge will be applied until a reservation is actually converted into a Rental.</h4>
				</center>
    	</div>
    </div>
</div>
<!--inner block end here-->
<?php
include('footer.php');
?>

