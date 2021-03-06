<?php

/*
 * Константы для сео-текстов
 */

class SeoText
{
    const ROLE_EMP = 'Заказчик';
    const ROLE_FRL = 'Фрилансер';
    const REMOTELY = '%s удаленно, ';

    const USER_NAME = '%s %s [%s]';

    const USER_TITLE_SPEC = '%s - %s, фриланс, FL.ru, %s, %s';
    const USER_TITLE_DEF = '%s %s - удаленная работа, фриланс, FL.ru, %s, %s';
    const USER_TITLE_BLOCKED = 'Аккаунт заблокирован [%s].';

    //[Предложение 2 прежней генерации].
    const USER_DESC_SPEC = 'Персональная страница фрилансера %s, %s, %s. Портфолио фрилансера, услуги по специализации: %s, %s. Отзывы о удаленной работе, контактная информация. Примеры работ по запросам: %s, %s, %s';
    const USER_DESC_DEF = 'Персональная страница фрилансера %s, %s, %s. Портфолио фрилансера, заказать услуги удаленно, фриланс. Отзывы о удаленной работе, контактная информация. Примеры работ фрилансера';

    const USER_KEY_SPEC = '%s, фриланс, фрилансер, freelance, %s удаленно, %s';
    const USER_KEY_DEF = '%s, фриланс, фрилансер, freelance, специалист удаленно, работа на дому, сотрудник онлайн, требуется специалист, Fl.ru';

    const EMP_DESC = 'Персональная страница заказчика %s, %s, %s. Профиль заказчика, работа на дому, работа удаленно, фриланс. Отзывы о удаленной работе, контактная информация. Вакансии для фрилансеров.';
    const EMP_KEY = '%s, фриланс, фрилансер, freelance, вакансии для фрилансеров, работа на дому, удаленные вакансии, Fl.ru';

    const PORTFOLIO_TITLE = 'Портфолио фрилансера %s. %s - %s. Фриланс, удаленная работа на FL.ru';
    const PORTFOLIO_DESC = 'Представляем работу фрилансера %s %s на FL.ru (%s): %s. Портфолио фрилансера %s, примеры выполненных работ.';

    const TAGS = 'Теги: %s';

    const IMAGE_DESC_DEF = 'Заказать услуги фрилансера удаленно, фриланс';

    const USER_FOOTER_DEF = 'Теги: фриланс, фрилансер, freelance, специалист удаленно, работа на дому, сотрудник онлайн, требуется специалист, Fl.ru';

    const FRL_TITLE_DEF = 'Фриланс сайт удаленной работы №1. Фрилансеры, лучшие специалисты, freelance : FL.ru - страница %d';
    const FRL_TITLE = '%s : %s, %s, фриланс, FL.ru - страница %d';
    const FRL_DESC = '%s, %s. Лучшие фрилансеры и удаленные сотрудники по запросам: %s';
    const FRL_KEY = 'фриланс, фрилансер, freelance, %s удаленно, %s';
    const FRL_IMG_DESC = 'Заказать услуги фрилансера удаленно, %s, %s';

    //const TU_TITLE = "%s : %s, %s, фриланс, FL.ru";
    const TU_TITLE = '%s %s- %s фрилансеров. Гарантии качества, отзывы об удаленной работе и примеры работ.';
    const TU_TITLE_PRICE = 'от %s ';
    const TU_TITLE_COUNT = 'более %s';
    const TU_TITLE_COUNT_LESS = 'много предложений';

    const TU_DESC = '%s, %s. Заказать по лучшим ценам. Тысячи фриланс услуг по запросам: %s';
    const TU_KEY = 'фриланс, фрилансер, freelance, %s удаленно, %s';
    const TU_IMG_DESC = 'Заказать услуги фрилансера удаленно, %s, %s';

    const TUC_TITLE = '%s – заказать за %d рублей. Фрилансер %s, %s, %s';
    const TUC_DESC = 'Предложение фрилансера %s: %s. Подробная информация о стоимости работ, условиях, дополнительных услугах.';
    const TUC_KEY = '%s, фриланс, фрилансер, freelance, %s удаленно, %s';
    const TUC_IMG_DESC = 'Заказать услугу фрилансера удаленно - %s %s';
    const TUC_TAGS = 'Теги: %s удаленно, %s';

    const PRJ_TITLE = '%s : %s, фриланс, FL.ru';
    const PRJ_DESC = 'Работа на дому: %s - %s';
    const PRJ_KEY = 'фриланс, фрилансер, freelance, %s удаленно, %s';
    const PRJ_TAGS = 'Теги: %s удаленно, %s';
}
