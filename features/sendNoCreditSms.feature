Feature:  Failing to send an SMS due to insufficient account credit

    Scenario: Attempting to send an SMS when the account has no credit
        Given the message content is "Payment reminder for your invoice." 
        And the recipient's phone number is "0698765432"
        When the SMS sending request is triggered
        Then the operation should fail to send the SMS
        And an error should indicate that there is not enough SMS credit
