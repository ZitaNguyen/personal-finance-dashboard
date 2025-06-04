Feature: User Registration

    @ResetDatabase
    Scenario: Successful registration
        Given I register with body:
            """
            {
                "email": "testbehat@example.com",
                "password": "secure456",
                "username": "Test Behat"
            }
            """
        Then the response code should be 201
        And the response should contain "User registered successfully, please check your email to verify your account."

    Scenario: Registration with already used email
        Given I register with body:
            """
            {
                "email": "testbehat@example.com",
                "password": "secure456",
                "username": "Test Behat"
            }
            """
        Then the response code should be 409
        And the response should contain "User registration failed. Email already exists."
