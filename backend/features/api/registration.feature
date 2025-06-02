Feature: User Registration

    @ResetDatabase
    Scenario: Successful registration
        When I send a POST request to "/api/register" with body:
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
        When I send a POST request to "/api/register" with body:
            """
            {
                "email": "testbehat@example.com",
                "password": "secure456",
                "username": "Test Behat"
            }
            """
        Then the response code should be 409
        And the response should contain "User registration failed. Email already exists."
