# Modularize Command Signatures Reference

This document contains all command signatures extracted from the source files.

## 1. module:list

**File:** `src/Console/Commands/ModuleListCommand.php`

**Signature:**
```
module:list
```

**Description:** List all existing modules in the project

**Arguments:** None

**Options:** None

---

## 2. module:make:component

**File:** `src/Console/Commands/ModuleMakeComponentCommand.php`

**Signature:**
```
module:make:component {name} {--module=} {--inline} {--view} {--test} {--pest} {--force}
```

**Description:** Generate view component for a module

**Arguments:**
- `name` : The name of the component

**Options:**
- `--module=` : Name of module controller should belong to
- `--inline` : Create a component that renders an inline view
- `--view` : Create an anonymous component with only a view
- `--test` : Generate an accompanying PHPUnit test for the Component
- `--pest` : Generate an accompanying Pest test for the Component
- `--force` : Create the class even if the component already exists

---

## 3. module:make:console

**File:** `src/Console/Commands/ModuleMakeConsoleCommand.php`

**Signature:**
```
module:make:console {name} {--module=} {--force}
```

**Description:** Generate console command for a module

**Arguments:**
- `name` : The name of the command

**Options:**
- `--module=` : Name of module controller should belong to
- `--force` : Create the class even if the component already exists

---

## 4. module:make:controller

**File:** `src/Console/Commands/ModuleMakeControllerCommand.php`

**Signature:**
```
module:make:controller {name} {--module=} {--api} {--i|invokable} {--r|resource} {--m|model=} {--force}
```

**Description:** Generate controller for a module

**Arguments:**
- `name` : The name of the controller

**Options:**
- `--module=` : Name of module controller should belong to
- `--api` : Exclude the create and edit methods from the controller
- `--i|invokable` : Generate a single method, invokable controller class
- `--r|resource` : Generate a resource controller class
- `--m|model=` : Generate a resource controller for the given model
- `--force` : Create the class even if the component already exists

---

## 5. module:make:event

**File:** `src/Console/Commands/ModuleMakeEventCommand.php`

**Signature:**
```
module:make:event {name} {--module=} {--force}
```

**Description:** Generate event for a module

**Arguments:**
- `name` : The name of the event

**Options:**
- `--module=` : Name of module event should belong to
- `--force` : Create the class even if the component already exists

---

## 6. module:make:factory

**File:** `src/Console/Commands/ModuleMakeFactoryCommand.php`

**Signature:**
```
module:make:factory {name} {--module=} {--model=} {--force}
```

**Description:** Generate factory for a module

**Arguments:**
- `name` : The name of the factory

**Options:**
- `--module=` : Name of module policy should belong to
- `--model=` : The name of the model for the factory
- `--force` : Create the class even if the component already exists

---

## 7. module:make:job

**File:** `src/Console/Commands/ModuleMakeJobCommand.php`

**Signature:**
```
module:make:job {name} {--module=} {--sync} {--force}
```

**Description:** Generate job for a module

**Arguments:**
- `name` : The name of the job

**Options:**
- `--module=` : Name of module job should belong to
- `--sync` : Indicates that job should be synchronous
- `--force` : Create the class even if the component already exists

---

## 8. module:make:listener

**File:** `src/Console/Commands/ModuleMakeListenerCommand.php`

**Signature:**
```
module:make:listener {name} {--module=} {--event=} {--queued} {--force}
```

**Description:** Generate listener for a module

**Arguments:**
- `name` : The name of the listener

**Options:**
- `--module=` : Name of module event should belong to
- `--event=` : The event class being listened for
- `--queued` : Indicates the event listener should be queued
- `--force` : Create the class even if the component already exists

---

## 9. module:make:mail

**File:** `src/Console/Commands/ModuleMakeMailCommand.php`

**Signature:**
```
module:make:mail {name} {--module=} {--markdown=} {--force}
```

**Description:** Generate mail for a module

**Arguments:**
- `name` : The name of the mail

**Options:**
- `--module=` : Name of module mail should belong to
- `--markdown=` : Create a new Markdown template for the mailable
- `--force` : Create the class even if the component already exists

---

## 10. module:make:middleware

**File:** `src/Console/Commands/ModuleMakeMiddlewareCommand.php`

**Signature:**
```
module:make:middleware {name} {--module=} {--force}
```

**Description:** Generate middleware for a module

**Arguments:**
- `name` : The name of the middleware

**Options:**
- `--module=` : Name of module middleware should belong to
- `--force` : Create the class even if the component already exists

---

## 11. module:make:migration

**File:** `src/Console/Commands/ModuleMakeMigrationCommand.php`

**Signature:**
```
module:make:migration {name} {--module=} {--create=} {--table=} {--no-translation}
```

**Description:** Generate migration for a module

**Arguments:**
- `name` : The name of the migration

**Options:**
- `--module=` : Name of module migration should belong to
- `--create=` : Name of the table to be created
- `--table=` : Name of the table to be updated
- `--no-translation` : Do not create module translation filesystem

---

## 12. module:make:model

**File:** `src/Console/Commands/ModuleMakeModelCommand.php`

**Signature:**
```
module:make:model {name} {--module=} {--a|all} {--c|controller} {--f|factory} {--force} {--m|migration} {--p|pivot} {--policy} {--s|seed} {--api} {--i|invokable} {--r|resource}
```

**Description:** Generate model for a module

**Arguments:**
- `name` : The name of the model

**Options:**
- `--module=` : Name of module controller should belong to
- `--a|all` : Generate a migration, seeder, factory, policy, resource controller, and form request classes for the model
- `--c|controller` : Create a new controller for the model
- `--f|factory` : Create a new factory for the model
- `--force` : Create the class even if the component already exists
- `--m|migration` : Create a new migration file for the model
- `--p|pivot` : Indicates if the generated model should be a custom intermediate table model
- `--policy` : Create a new policy for the model
- `--s|seed` : Create a new seeder for the model
- `--api` : Exclude the create and edit methods from the controller
- `--i|invokable` : Generate a single method, invokable controller class
- `--r|resource` : Generate a resource controller class

---

## 13. module:make:notification

**File:** `src/Console/Commands/ModuleMakeNotificationCommand.php`

**Signature:**
```
module:make:notification {name} {--module=} {--model=} {--guard=} {--force}
```

**Description:** Generate notification for a module

**Arguments:**
- `name` : The name of the notification

**Options:**
- `--module=` : Name of module migration should belong to
- `--model=` : The model that the policy applies to
- `--guard=` : The guard that the policy relies on
- `--force` : Create the class even if the component already exists

---

## 14. module:make:policy

**File:** `src/Console/Commands/ModuleMakePolicyCommand.php`

**Signature:**
```
module:make:policy {name} {--module=} {--model=} {--guard=}
```

**Description:** Generate policy for a module

**Arguments:**
- `name` : The name of the policy

**Options:**
- `--module=` : Name of module policy should belong to
- `--model=` : The model that the policy applies to
- `--guard=` : The guard that the policy relies on

---

## 15. module:make:provider

**File:** `src/Console/Commands/ModuleMakeProviderCommand.php`

**Signature:**
```
module:make:provider {name} {--module=} {--force}
```

**Description:** Generate provider for a module

**Arguments:**
- `name` : The name of the provider

**Options:**
- `--module=` : Name of module migration should belong to
- `--force` : Create the class even if the component already exists

---

## 16. module:make:request

**File:** `src/Console/Commands/ModuleMakeRequestCommand.php`

**Signature:**
```
module:make:request {name} {--module=} {--force}
```

**Description:** Generate form request for a module

**Arguments:**
- `name` : The name of the request

**Options:**
- `--module=` : Name of module migration should belong to
- `--force` : Create the class even if the component already exists

---

## 17. module:make:resource

**File:** `src/Console/Commands/ModuleMakeResourceCommand.php`

**Signature:**
```
module:make:resource {name} {--module=} {--collection} {--force}
```

**Description:** Generate resource for a module

**Arguments:**
- `name` : The name of the resource

**Options:**
- `--module=` : Name of module migration should belong to
- `--collection` : Create a resource collection
- `--force` : Create the class even if the component already exists

---

## 18. module:make:seeder

**File:** `src/Console/Commands/ModuleMakeSeederCommand.php`

**Signature:**
```
module:make:seeder {name} {--module=} {--model=} {--force}
```

**Description:** Generate seeder for a module

**Arguments:**
- `name` : The name of the seeder

**Options:**
- `--module=` : Name of module seeder should belong to
- `--model=` : The name of the model for the seeder
- `--force` : Create the class even if the component already exists

---

## 19. module:make:test

**File:** `src/Console/Commands/ModuleMakeTestCommand.php`

**Signature:**
```
module:make:test {name} {--module=} {--u|unit} {--p|pest} {--view} {--force}
```

**Description:** Generate test for a module

**Arguments:**
- `name` : The name of the test

**Options:**
- `--module=` : Name of module migration should belong to
- `--u|unit` : Create a unit test
- `--p|pest` : Create a Pest test
- `--view` : Create a view test
- `--force` : Create the class even if the component already exists

---

## 20. module:make:view

**File:** `src/Console/Commands/ModuleMakeViewCommand.php`

**Signature:**
```
module:make:view {name} {--module=} {--test} {--pest} {--force}
```

**Description:** Generate view for a module

**Arguments:**
- `name` : The name of the view

**Options:**
- `--module=` : Name of module migration should belong to
- `--test` : Generate an accompanying PHPUnit test for the View
- `--pest` : Generate an accompanying Pest test for the View
- `--force` : Create the class even if the component already exists

---

## Summary

Total commands: 20

### Command Categories:

**Listing:**
1. `module:list`

**Code Generation:**
2. `module:make:component`
3. `module:make:console`
4. `module:make:controller`
5. `module:make:event`
6. `module:make:factory`
7. `module:make:job`
8. `module:make:listener`
9. `module:make:mail`
10. `module:make:middleware`
11. `module:make:migration`
12. `module:make:model`
13. `module:make:notification`
14. `module:make:policy`
15. `module:make:provider`
16. `module:make:request`
17. `module:make:resource`
18. `module:make:seeder`
19. `module:make:test`
20. `module:make:view`

### Common Options Across Commands:

- `--module=` : Specifies the module name (present in all make commands)
- `--force` : Create the class even if it already exists (present in most commands)
- `--test` / `--pest` : Generate accompanying tests (component, view)
- `--model=` : Associate with a model (factory, seeder, notification, policy)
