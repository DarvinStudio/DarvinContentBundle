Виджеты контента
================

## Описание

Виджеты контента позволяют включать в контент placeholder'ы, которые будут заменены при выводе на некое строковое значение,
 определяемое реализацией виджета.

## Создание

**1. Создаем класс, реализующий "Darvin\ContentBundle\Widget\WidgetInterface":**

```php
use Darvin\ContentBundle\Widget\WidgetInterface;

class Widget implements WidgetInterface
{
    public function getContent()
    {
        return 'bar';
    }

    public function getPlaceholder()
    {
        return '%foo%';
    }

    public function getName()
    {
        return 'foo';
    }
}
```

**2. Объявляем класс сервисом и помечаем его тегом "darvin_content.widget":**

```yaml
parameters:
    app.widget.foo.class: App\Widget\FooWidget

services:
    app.widget.foo:
        class: "%app.widget.foo.class%"
        tags:
            - { name: darvin_content.widget }
```

## Использование

Для замены placeholder'ов в контенте можно воспользоваться методом "embed()" сервиса "darvin_content.widget.embedder":

```php
$content = 'test %foo% test';
$content = $this->getContainer()->get('darvin_content.widget.embedder')->embed($content);
echo $content; // 'test bar test'
```

или использовать фильтр Twig "content_embed_widgets".
