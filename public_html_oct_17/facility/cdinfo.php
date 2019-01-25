<?php
$GError="";
include('header.php');
include('../sql.php');

function stripe_curl($user, $url, $posts){
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_POSTFIELDS, 'source='.$posts['source'].'&email='.$posts['email'].'&description='.$posts['description']);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type' => 'application/x-www-form-urlencoded'));
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_USERNAME, $user);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_POST, true);
  $data = curl_exec($ch);
  curl_close($ch);
  return $data;
}

if(isset($_POST['stripeToken'])){
  //echo ('Successfully received token: '.$_POST['stripeToken']);
  error_log('Successfully received token: '.$_POST['stripeToken']);
  $query = "select * from owner_card where owner_id = '".$_SESSION['lfdata']['auto_id']."'";
  $res = mysqli_query($conn, $query);
  if(mysqli_num_rows($res) == 0){
    $query = "insert into owner_card (owner_id, stripe_id, stripe_token) values ('".$_SESSION['lfdata']['auto_id']."', '".$_POST['stripeToken']."', '".$_POST['stripeToken']."')";
  }
  else{
    $query = "update owner_card set stripe_id='".$_POST['stripeToken']."' and stripe_token='".$_POST['stripeToken']."' where owner_id='".$_SESSION['lfdata']['auto_id']."'";
  }
  mysqli_query($conn, $query) or die('Failed to update owner card token. Please try again in some time.');
  
  /*$url = 'https://api.stripe.com/v1/customers';
  $user = 'pk_test_TYooMQauvdEDq54NiTphI7jx';
  $posts = array('source' => $_POST['stripeToken'], 
                 'email' => $_SESSION['lfdata']['emailid'],
                 'description' => 'Customer for '.$_SESSION['lfdata']['auto_id'].':'.$_SESSION['lfdata']['firstname'].' '.$_SESSION['lfdata']['lastname']);
  $ret = stripe_curl($user, $url, $posts);
  error_log($ret);
  $dev_ret = json_decode($ret, true);
  */
  //error_log($dev_ret['id'].' : '.$dev_ret['object'].' : '.$dev_ret['created'].' : '.$dev_ret['description'].' : '.$dev_ret['email'].' : '.$dev_ret['sources']['data'][0]);
  header("Location: dashboard.php");
}

?>
<script>
function ajaxcall_self(form, datastring){
    var res;
    $.ajax
    ({	
    		type:"POST",
    		url:"cdinfo.php",
    		data:datastring,
    		cache:false,
    		async:true,
    		success: function(result){		
   				 	res = result;
   				 	form.querySelector("#s_message").innerText = 'Succesfully saved your credit card details. You may proceed to the dashboard now.';
   		 	},
   		 	error: function(err){
   		 	    alert('Failed to invoke serverside function(in cdinfo)... Please try again in some time');
   		 	    res = false;
   		 	}
    });
    return res;
}
</script>
<!--inner block start here-->
<div class="inner-block">
    <div class="blank">
    	<h2>Nearly there ...</h2>
    	<div class="blankpage-main">
    		<center>
    			<h4>Credit Card Details</h4>
    			<hr>
					<?php
					if($GError!=""){
						echo "<div class=\"alert alert-info\" role=\"alert\">".$GError."</div>";
					}
					?>
					<center><img src="../images/emsurvey2.png" /></center>
          
          <script src="https://js.stripe.com/v3/"></script>
          <!-- script src="js/stripehelper.js"></script -->
          <script>
            $(document).ready(function() {
              'use strict';
               var stripe = Stripe('pk_test_TYooMQauvdEDq54NiTphI7jx');

                function registerElements(elements, exampleName) {
                  var formClass = '.' + exampleName;
                  var example = document.querySelector(formClass);

                  var form = example.querySelector('form');
                  var error = form.querySelector('.error');
                  ////var errorMessage = error.querySelector('.message');

                  function enableInputs() {
                    Array.prototype.forEach.call(
                        form.querySelectorAll("input[type='text'], input[type='email'], input[type='tel']"),
                        function(input) {
                          input.removeAttribute('disabled');
                        }
                    );
                  }
  
                  function disableInputs() {
                    Array.prototype.forEach.call(
                      form.querySelectorAll("input[type='text'], input[type='email'], input[type='tel']"),
                      function(input) {
                        input.setAttribute('disabled', 'true');
                      }
                    );
                  }
    
                  function triggerBrowserValidation() {
                    var submit = document.createElement('input');
                    submit.type = 'submit';
                    submit.style.display = 'none';
                    form.appendChild(submit);
                    submit.click();
                    submit.remove();
                  }
    
                  var savedErrors = {};
                  elements.forEach(function(element, idx) {
                    element.on('change', function(event) {
                      if (event.error) {
                        error.classList.add('visible');
                        savedErrors[idx] = event.error.message;
                        //// errorMessage.innerText = event.error.message;
                        console.log("Error message 1 :" + event.error.message);
                      } else {
                        savedErrors[idx] = null;
      
                        // Loop over the saved errors and find the first one, if any.
                        var nextError = Object.keys(savedErrors)
                                              .sort()
                                              .reduce(function(maybeFoundError, key) {
                        return maybeFoundError || savedErrors[key];
                        }, null);
      
                        if (nextError) {
                          // Now that they've fixed the current error, show another one.
                          ////errorMessage.innerText = nextError;
                          console.log("Error message 2 :" + event.error.message);
                        } else {
                          // The user fixed the last error; no more errors.
                          error.classList.remove('visible');
                        }
                      }
                    });
                  });
    
                  // Listen on the form's 'submit' handler...
                  form.addEventListener('submit', function(e) {
                    e.preventDefault();
    
                    // Trigger HTML5 validation UI on the form if any of the inputs fail
                    // validation.
                    var plainInputsValid = true;
                    Array.prototype.forEach.call(form.querySelectorAll('input'), function(
                      input
                    ){
                      if (input.checkValidity && !input.checkValidity()) {
                        plainInputsValid = false;
                        return;
                      }
                    });
                    if (!plainInputsValid) {
                      triggerBrowserValidation();
                      return;
                    }
    
                    // Show a loading screen...
                    example.classList.add('submitting');
    
                    // Disable all inputs.
                    disableInputs();
    
                    var additionalData = {
                      //name: name ? name.value : undefined,
                      //address_line1: address1 ? address1.value : undefined,
                      //address_city: city ? city.value : undefined,
                      //address_state: state ? state.value : undefined,
                      //address_zip: zip ? zip.value : undefined,
                    };
    
                    // Use Stripe.js to create a token. We only need to pass in one Element
                    // from the Element group in order to create a token. We can also pass
                    // in the additional customer data we collected in our form.
                    stripe.createToken(elements[0], additionalData).then(function(result) {
                      // Stop loading!
                      example.classList.remove('submitting');
    
                      if (result.token) {
                        // If we received a token, show the token ID.
                        //// example.querySelector('.token').innerText = result.token.id;
                        console.log("GOT TOKEN: " + result.token.id);
                        example.classList.add('submitted');
                        ajaxcall_self(form, 'stripeToken=' + result.token.id);
                      } else {
                        // Otherwise, un-disable inputs.
                        enableInputs();
                      }
                    });
                  });
    
      /*resetButton.addEventListener('click', function(e) {
        e.preventDefault();
        // Resetting the form (instead of setting the value to `''` for each input)
        // helps us clear webkit autofill styles.
        form.reset();
    
        // Clear each Element.
        elements.forEach(function(element) {
          element.clear();
        });
    
        // Reset error state as well.
        error.classList.remove('visible');
    
        // Resetting the form does not un-disable inputs, so we need to do it separately:
        enableInputs();
        example.classList.remove('submitted');
      });*/
                }
                  //// End of registerElements()...

                var elements = stripe.elements({
                                                fonts: [
                                                        {
                                                          cssSrc: 'https://fonts.googleapis.com/css?family=Source+Code+Pro',
                                                        },
                                                ],
                  // Stripe's examples are localized to specific languages, but if
                  // you wish to have Elements automatically detect your user's locale,
                  // use `locale: 'auto'` instead.
                  locale: window.__exampleLocale
                });

        // Floating labels
        var inputs = document.querySelectorAll('.cell.example.example2 .input');
        Array.prototype.forEach.call(inputs, function(input) {
              input.addEventListener('focus', function() {
                input.classList.add('focused');
              });
              input.addEventListener('blur', function() {
                input.classList.remove('focused');
              });
              input.addEventListener('keyup', function() {
                if (input.value.length === 0) {
                  input.classList.add('empty');
                } else {
                  input.classList.remove('empty');
                }
              });
        });

        var elementStyles = {
          base: {
          color: '#32325D',
          fontWeight: 500,
          fontFamily: 'Source Code Pro, Consolas, Menlo, monospace',
          fontSize: '16px',
          fontSmoothing: 'antialiased',

          '::placeholder': {
            color: '#CFD7DF',
          },
          ':-webkit-autofill': {
            color: '#e39f48',
          },
        },
        invalid: {
          color: '#E25950',

          '::placeholder': {
            color: '#FFCCA5',
          },
        },
      };

      var elementClasses = {
        focus: 'focused',
        empty: 'empty',
        invalid: 'invalid',
      };

      var cardNumber = elements.create('cardNumber', {
        style: elementStyles,
        classes: elementClasses,
      });
      cardNumber.mount('#example2-card-number');

      var cardExpiry = elements.create('cardExpiry', {
        style: elementStyles,
        classes: elementClasses,
      });
      cardExpiry.mount('#example2-card-expiry');

      var cardCvc = elements.create('cardCvc', {
        style: elementStyles,
        classes: elementClasses,
      });
      cardCvc.mount('#example2-card-cvc');

      registerElements([cardNumber, cardExpiry, cardCvc], 'example2');
    });
  </script>
          
          <!-- script>
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
  
    </script-->

    <link rel="stylesheet" type="text/css" href="css/stripehelper.css" />
<!-- style>
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
</style -->
        <div class="cell example example2" id="example-2">
        <form>
          <!-- div data-locale-reversible="">
            <div class="row">
              <div class="field">
                <input id="example2-address" data-tid="elements_examples.form.address_placeholder" class="input empty" type="text" placeholder="185 Berry St" required="" autocomplete="address-line1">
                <label for="example2-address" data-tid="elements_examples.form.address_label">Address</label>
                <div class="baseline"></div>
              </div>
            </div>
            <div class="row" data-locale-reversible="">
              <div class="field half-width">
                <input id="example2-city" data-tid="elements_examples.form.city_placeholder" class="input empty" type="text" placeholder="San Francisco" required="" autocomplete="address-level2">
                <label for="example2-city" data-tid="elements_examples.form.city_label">City</label>
                <div class="baseline"></div>
              </div>
              <div class="field quarter-width">
                <input id="example2-state" data-tid="elements_examples.form.state_placeholder" class="input empty" type="text" placeholder="CA" required="" autocomplete="address-level1">
                <label for="example2-state" data-tid="elements_examples.form.state_label">State</label>
                <div class="baseline"></div>
              </div>
              <div class="field quarter-width">
                <input id="example2-zip" data-tid="elements_examples.form.postal_code_placeholder" class="input empty" type="text" placeholder="94107" required="" autocomplete="postal-code">
                <label for="example2-zip" data-tid="elements_examples.form.postal_code_label">ZIP</label>
                <div class="baseline"></div>
              </div>
            </div>
          </div -->

          <div class="row">
            <div class="field">
              <div id="example2-card-number" class="input empty StripeElement"><div class="__PrivateStripeElement" style="margin: 0px !important; padding: 0px !important; border: none !important; display: block !important; background: transparent !important; position: relative !important; opacity: 1 !important;"><iframe frameborder="0" allowtransparency="true" scrolling="no" name="__privateStripeFrame7" allowpaymentrequest="true" src="https://js.stripe.com/v3/elements-inner-card-6c1512b4abd5985e1d226b290314bc48.html#style[base][color]=%2332325D&amp;style[base][fontWeight]=500&amp;style[base][fontFamily]=Source+Code+Pro%2C+Consolas%2C+Menlo%2C+monospace&amp;style[base][fontSize]=16px&amp;style[base][fontSmoothing]=antialiased&amp;style[base][::placeholder][color]=%23CFD7DF&amp;style[base][:-webkit-autofill][color]=%23e39f48&amp;style[invalid][color]=%23E25950&amp;style[invalid][::placeholder][color]=%23FFCCA5&amp;locale=en&amp;componentName=cardNumber&amp;wait=true&amp;rtl=false&amp;keyMode=test&amp;origin=https%3A%2F%2Fstripe.github.io&amp;referrer=https%3A%2F%2Fstripe.github.io%2Felements-examples%2F&amp;controllerId=__privateStripeController1" title="Secure payment input frame" style="border: none !important; margin: 0px !important; padding: 0px !important; width: 1px !important; min-width: 100% !important; overflow: hidden !important; display: block !important; height: 19.2px;"></iframe><input class="__PrivateStripeElement-input" aria-hidden="true" aria-label=" " autocomplete="false" maxlength="1" style="border: none !important; display: block !important; position: absolute !important; height: 1px !important; top: 0px !important; left: 0px !important; padding: 0px !important; margin: 0px !important; width: 100% !important; opacity: 0 !important; background: transparent !important; pointer-events: none !important; font-size: 16px !important;"></div></div>
              <label for="example2-card-number" data-tid="elements_examples.form.card_number_label">Card number</label>
              <div class="baseline"></div>
            </div>
          </div>
          <div class="row">
            <div class="field half-width">
              <div id="example2-card-expiry" class="input empty StripeElement"><div class="__PrivateStripeElement" style="margin: 0px !important; padding: 0px !important; border: none !important; display: block !important; background: transparent !important; position: relative !important; opacity: 1 !important;"><iframe frameborder="0" allowtransparency="true" scrolling="no" name="__privateStripeFrame8" allowpaymentrequest="true" src="https://js.stripe.com/v3/elements-inner-card-6c1512b4abd5985e1d226b290314bc48.html#style[base][color]=%2332325D&amp;style[base][fontWeight]=500&amp;style[base][fontFamily]=Source+Code+Pro%2C+Consolas%2C+Menlo%2C+monospace&amp;style[base][fontSize]=16px&amp;style[base][fontSmoothing]=antialiased&amp;style[base][::placeholder][color]=%23CFD7DF&amp;style[base][:-webkit-autofill][color]=%23e39f48&amp;style[invalid][color]=%23E25950&amp;style[invalid][::placeholder][color]=%23FFCCA5&amp;locale=en&amp;componentName=cardExpiry&amp;wait=true&amp;rtl=false&amp;keyMode=test&amp;origin=https%3A%2F%2Fstripe.github.io&amp;referrer=https%3A%2F%2Fstripe.github.io%2Felements-examples%2F&amp;controllerId=__privateStripeController1" title="Secure payment input frame" style="border: none !important; margin: 0px !important; padding: 0px !important; width: 1px !important; min-width: 100% !important; overflow: hidden !important; display: block !important; height: 19.2px;"></iframe><input class="__PrivateStripeElement-input" aria-hidden="true" aria-label=" " autocomplete="false" maxlength="1" style="border: none !important; display: block !important; position: absolute !important; height: 1px !important; top: 0px !important; left: 0px !important; padding: 0px !important; margin: 0px !important; width: 100% !important; opacity: 0 !important; background: transparent !important; pointer-events: none !important; font-size: 16px !important;"></div></div>
              <label for="example2-card-expiry" data-tid="elements_examples.form.card_expiry_label">Expiration</label>
              <div class="baseline"></div>
            </div>
            <div class="field half-width">
              <div id="example2-card-cvc" class="input empty StripeElement"><div class="__PrivateStripeElement" style="margin: 0px !important; padding: 0px !important; border: none !important; display: block !important; background: transparent !important; position: relative !important; opacity: 1 !important;"><iframe frameborder="0" allowtransparency="true" scrolling="no" name="__privateStripeFrame9" allowpaymentrequest="true" src="https://js.stripe.com/v3/elements-inner-card-6c1512b4abd5985e1d226b290314bc48.html#style[base][color]=%2332325D&amp;style[base][fontWeight]=500&amp;style[base][fontFamily]=Source+Code+Pro%2C+Consolas%2C+Menlo%2C+monospace&amp;style[base][fontSize]=16px&amp;style[base][fontSmoothing]=antialiased&amp;style[base][::placeholder][color]=%23CFD7DF&amp;style[base][:-webkit-autofill][color]=%23e39f48&amp;style[invalid][color]=%23E25950&amp;style[invalid][::placeholder][color]=%23FFCCA5&amp;locale=en&amp;componentName=cardCvc&amp;wait=true&amp;rtl=false&amp;keyMode=test&amp;origin=https%3A%2F%2Fstripe.github.io&amp;referrer=https%3A%2F%2Fstripe.github.io%2Felements-examples%2F&amp;controllerId=__privateStripeController1" title="Secure payment input frame" style="border: none !important; margin: 0px !important; padding: 0px !important; width: 1px !important; min-width: 100% !important; overflow: hidden !important; display: block !important; height: 19.2px;"></iframe><input class="__PrivateStripeElement-input" aria-hidden="true" aria-label=" " autocomplete="false" maxlength="1" style="border: none !important; display: block !important; position: absolute !important; height: 1px !important; top: 0px !important; left: 0px !important; padding: 0px !important; margin: 0px !important; width: 100% !important; opacity: 0 !important; background: transparent !important; pointer-events: none !important; font-size: 16px !important;"></div></div>
              <label for="example2-card-cvc" data-tid="elements_examples.form.card_cvc_label">CVC</label>
              <div class="baseline"></div>
            </div>
          </div>
        <button type="submit" data-tid="elements_examples.form.pay_button">Save Details</button>
          <div class="error" role="alert"><svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17">
              <!-- path class="base" fill="#000" d="M8.5,17 C3.80557963,17 0,13.1944204 0,8.5 C0,3.80557963 3.80557963,0 8.5,0 C13.1944204,0 17,3.80557963 17,8.5 C17,13.1944204 13.1944204,17 8.5,17 Z"></path -->
              <path class="glyph" fill="#FFF" d="M8.5,7.29791847 L6.12604076,4.92395924 C5.79409512,4.59201359 5.25590488,4.59201359 4.92395924,4.92395924 C4.59201359,5.25590488 4.59201359,5.79409512 4.92395924,6.12604076 L7.29791847,8.5 L4.92395924,10.8739592 C4.59201359,11.2059049 4.59201359,11.7440951 4.92395924,12.0760408 C5.25590488,12.4079864 5.79409512,12.4079864 6.12604076,12.0760408 L8.5,9.70208153 L10.8739592,12.0760408 C11.2059049,12.4079864 11.7440951,12.4079864 12.0760408,12.0760408 C12.4079864,11.7440951 12.4079864,11.2059049 12.0760408,10.8739592 L9.70208153,8.5 L12.0760408,6.12604076 C12.4079864,5.79409512 12.4079864,5.25590488 12.0760408,4.92395924 C11.7440951,4.59201359 11.2059049,4.59201359 10.8739592,4.92395924 L8.5,7.29791847 L8.5,7.29791847 Z"></path>
            </svg>
            <span class="message" id="s_message"></span></div>
        </form>
        <!-- div class="success">
          <div class="icon">
            <svg width="84px" height="84px" viewBox="0 0 84 84" version="1.1" xmlns="http://www.w3.org/2000/svg" xlink="http://www.w3.org/1999/xlink">
              <circle class="border" cx="42" cy="42" r="40" stroke-linecap="round" stroke-width="4" stroke="#000" fill="none"></circle>
              <path class="checkmark" stroke-linecap="round" stroke-linejoin="round" d="M23.375 42.5488281 36.8840688 56.0578969 64.891932 28.0500338" stroke-width="4" stroke="#000" fill="none"></path>
            </svg>
          </div>
          <!-- h3 class="title" data-tid="elements_examples.success.title">Payment successful</h3 -->
          <!-- p class="message"><span data-tid="elements_examples.success.message">Thanks for trying Stripe Elements. No money was charged, but we generated a token:</span><span class="token">tok_189gMN2eZvKYlo2CwTBv9KKh</span></p -->
          <!-- a class="reset" href="#">
            <svg width="32px" height="32px" viewBox="0 0 32 32" version="1.1" xmlns="http://www.w3.org/2000/svg" xlink="http://www.w3.org/1999/xlink">
              <path fill="#000000" d="M15,7.05492878 C10.5000495,7.55237307 7,11.3674463 7,16 C7,20.9705627 11.0294373,25 16,25 C20.9705627,25 25,20.9705627 25,16 C25,15.3627484 24.4834055,14.8461538 23.8461538,14.8461538 C23.2089022,14.8461538 22.6923077,15.3627484 22.6923077,16 C22.6923077,19.6960595 19.6960595,22.6923077 16,22.6923077 C12.3039405,22.6923077 9.30769231,19.6960595 9.30769231,16 C9.30769231,12.3039405 12.3039405,9.30769231 16,9.30769231 L16,12.0841673 C16,12.1800431 16.0275652,12.2738974 16.0794108,12.354546 C16.2287368,12.5868311 16.5380938,12.6540826 16.7703788,12.5047565 L22.3457501,8.92058924 L22.3457501,8.92058924 C22.4060014,8.88185624 22.4572275,8.83063012 22.4959605,8.7703788 C22.6452866,8.53809377 22.5780351,8.22873685 22.3457501,8.07941076 L22.3457501,8.07941076 L16.7703788,4.49524351 C16.6897301,4.44339794 16.5958758,4.41583275 16.5,4.41583275 C16.2238576,4.41583275 16,4.63969037 16,4.91583275 L16,7 L15,7 L15,7.05492878 Z M16,32 C7.163444,32 0,24.836556 0,16 C0,7.163444 7.163444,0 16,0 C24.836556,0 32,7.163444 32,16 C32,24.836556 24.836556,32 16,32 Z"></path>
            </svg>
          </a -->
        <!-- /div -->
      </div>
          
          <!-- form action="/facility/cdinfo.php" method="post" id="payment-form">

            <div class="form-row">
            <label for="card-element">
              Credit Card Number
            </label>
            <div id="#example2-card-number">
              
            </div>
            <img src="../images/shield.jpg" height="7%" width="7%" />

            <div id="card-errors" role="alert"></div>
            </div>

            <button>Submit</button>
          </form -->
          <img src="../images/shield.jpg" height="17%" width="17%" />
          <br /><h4>Note: No charge will be applied until a reservation is actually converted into a Rental.</h4>
				</center>
    	</div>
    </div>
</div>
<!--inner block end here-->
<?php
include('footer.php');
?>

