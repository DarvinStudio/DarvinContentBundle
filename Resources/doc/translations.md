Переводы
========

## Создание перевода сущности

1. Создаем сущность-перевод, по умолчанию имеющую название "<Название переводимой сущности> + Translation", и
 располагающуюся в том же пространстве имен.

2. Включаем в созданный класс трейт "Knp\DoctrineBehaviors\Model\Translatable\Translation".

3. Включаем в класс переводимой сущности трейт "Darvin\ContentBundle\Traits\TranslatableTrait".