Feature: User Connexion

    @ResetDatabase
    Scenario: Successful connexion
        Given I register with body:
            """
            {
                "email": "testbehat@example.com",
                "password": "secure456",
                "username": "Test Behat"
            }
            """
        And I receive a message confirming successful registration
        When I connect with this account:
            """
            {
                "email": "testbehat@example.com",
                "password": "secure456"
            }
            """
        Then the response code should be 200
        And the response should contain "token"

    Scenario: Failed connexion due to wrong password
        When I connect with this account:
            """
            {
                "email": "testbehat@example.com",
                "password": "secure"
            }
            """
        Then the response code should be 401
        And the response should contain "Invalid credentials."
