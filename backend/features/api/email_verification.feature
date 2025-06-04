Feature: Email verification
    In order to activate a newly registered account
    As a user
    I need to verify my email via the verification link

    @ResetDatabase
    Scenario: Successful email verification
        Given I register with body:
            """
            {
                "email": "testbehat@example.com",
                "password": "secure456",
                "username": "Test Behat"
            }
            """
        And I receive a message confirming successful registration
        Then I should receive a verification email
        When I visit the verification link from the email
        Then the response should contain "Email verified successfully."
