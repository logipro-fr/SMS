Feature: Sending SMS

    Scenario: Sending a simple SMS to a recipient
        Given the text to send "This is a message !" 
        Given the phone number is "0612345678"
        When the user submits a request to send SMS
        Then the system sends the SMS to the specified number
        And the system returns an acknowledgment indicating that the message has been transmitted to the SMS service provider




