# GitConnector Module

## Overview
This module provides a unified interface for interacting with various Git platforms. It includes a factory for creating Git connectors and a caching system to optimize API calls.
The initial version supports GitHub, with a structure that allows easy extension to other Git platforms, like GitLab.

## Getting Started
### Prerequisites
- Ensure you have the necessary permissions to access the repositories you intend to interact with
- Obtain the base URL for the API of the Git platform you are using

### Usage
To use the module, call the static method of the `GitInterfaceFactory` class. Provide the `GitPlatform` enum value corresponding to the Git platform you want to interact with, along with the repository name, owner, and the base API URL:
```php
$gitConnector = GitInterfaceFactory::create(GitPlatform::GITHUB, "dolibarr_module_scrumproject", "ATM-Consulting", "https://api.github.com");
```
Then, you can use the method you want, to do the needed stuff:
```php
$branches = $gitConnector->getBranches();
```

### Error Handling
Methods in the connectors will return the expected value (e.g. array, string) or throw a `GitException` in case of an error. If no exception is caught, the method call was successful.
In the caught exception, you can retrieve the error message, the error code (returns from the API), and request information (url, method, and context).
```php
try {
    $gitConnector = GitInterfaceFactory::create(GitPlatform::GITHUB, "dolibarr_module_scrumproject", "ATM-Consulting", "https://api.github.com");
    $mainBranch = $gitConnector->getBranch("main");
    $lastCommitAuthor = $mainBranch["commit"]["author"]["login"];
} catch (GitException $exception) {
    $message = $exception->getMessage(); // An error has occurred in the API call: Branch not found 
    $code = $exception->getCode();       // 404
    $context = $exception->getContext();    // [ "url" => "https://api.github.com/repos/ATM-Consulting/dolibarr_module_scrumproject/branches/main", "method" => "GET", "data" => []]
}
```

### Extending the Module
#### Adding a new Git Platform
1. **Enum Value**: Add the new platform as a value in the `GitPlatform` enum (`class/GitPlatform.php`)
2. **Connector Class**: Create a new class named `Git{Platform}Interface` (e.g. `GitLabInterface`) that implements the `GitInterface` abstract class
3. **Implement Methods**: Implement all required methods from the `GitInterface` class to ensure full functionality with the new platform

#### Adding a new API Call
1. **Method Definition**: Add the method to the relevant connector class. If the method should be available for any platform, add it as an abstract method in the `GitInterface` class (you must then implement it for every other git platform interface)
2. **API Call**: Within the method, call `$this->getCurlInstance($apiEndpoint)`, with `$apiEndpoint` can be a relative path from the BASE_URL, or a full path. You can also provide an array of cURL options as the second parameter (e.g. POST data)
3. **Result Handling**: Call `$this->getCurlResult($curl)` with `$curl` is the result of the previous step, and process or return the response as needed

## Postman Collection
### GitHub Collection
The repository contains a file named `API_GitHub.postman_collection.json` at the root level. This file can be imported into Postman to access a collection of API calls implemented in the `GitHubInterface` class. This collection provides a convenient way to test and interact with the API endpoints. 
**Note**: Remember to update this Postman collection whenever new API calls are added to the module to ensure it remains comprehensive and up-to-date.

### New Git Platform implements
For new platform implementations in the module, you should also add a ready-to-use postman collection, so that you can quickly test and verify the endpoints for this new platform.
