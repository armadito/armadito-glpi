Tests
=====

> It’s overwhelmingly easy to write bad unit tests that add very little value to a project while inflating the cost of code changes astronomically.

Steve Sanderson.

Thus, and because it is not a Test-driven Development project, we intentionally don't do any unit tests.
In contrast, we use integration tests to avoid regression.
Consequently, Travis-CI has been configured in order to do all these integration tests regularly.
Coverage is then updated in [sonarqube.com](https://sonarqube.com/component_measures/metric/coverage/list?id=armadito%3Aglpi%3ADEV%3ADEV)
