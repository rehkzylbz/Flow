# Flow
Реализовано подключение к API https://test.mgc-loyalty.ru/
Данные сохранены в БД sqlite.


С главной страницы есть возможность провести обновление содержимого БД (с перезаписью всех данных).
Стоит учитывать, что полное обновление может занять продолжительное время - до трёх минут (время ответа API на запросы списка товаров с максимальным шагом пагинации - порядка 10 с).


На странице категорий - структура с раскрывающимся списками и количеством товаров в категориях.
На странице категории - список товаров с основными данными (фото, название, описание, цена).
Базовые настройки - файл settings.php в корне.
