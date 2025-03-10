# CHANGELOG GitConnector

## Unreleased

## 0.1 - 2025-03-10
- **Git Factory**: Implemented a factory for creating Git connectors based on the specified Git platform
- **GitHub Connector**: Added a Git connector for the GitHub platform
- **API Calls**:
  - **List Pull Requests**: Retrieve the list of pull requests for a repository
  - **Create Branch**: Create a new branch in a repository
  - **List Branches**: Retrieve the list of branches in a repository
  - **Branch Info**: Retrieve information for a specific branch in a repository
  - **File Info**: Retrieve information of a file in a repository (including its content)
  - **Update File**: Update the content of a file in a repository
  - **Caching System**: Implemented a caching system to avoid redundant API calls, improving performance and efficiency
