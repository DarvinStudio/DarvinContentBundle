Контроллеры контента
====================

## Описание

Контентной является сущность, в классе которой используется аннотация "Darvin\Utils\Mapping\Annotation\Slug". Так как
 slug'и всех контентных сущностей хранятся в единой карте, существует front-контроллер, пытающийся найти по ней сущность
 и передающий управление соответствующему контроллеру контента в случае успеха. Поэтому для каждой контентной сущности
 должен существовать свой контроллер.

## Создание

**1. Создаем класс, реализующий "Darvin\ContentBundle\Controller\ContentControllerInterface".**

В метод "showAction()" вторым аргументом будет передаваться найденная контентная сущность.

Метод "getContentClass()" должен возвращать класс контентной сущности.

**2. Объявляем класс сервисом и помечаем его тегом "darvin_content.controller":**

```yaml
parameters:
    app.page.controller.class: App\Controller\PageController

services:
    app.page.controller:
        class: '%app.page.controller.class%'
        tags:
            - { name: darvin_content.controller }
```
