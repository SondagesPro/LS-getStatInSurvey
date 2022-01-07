# getStatInSurvey

Allow to show some statistics from previous answers in survey in question text, help and answers.


## Documentation

You can show statistics from these question type : 
* short text
* single choice (dropdown, radio, 5 point, Gender …)
* numeric 
* equation

You use the code of question (Qcode), the statistic you want and, optionally, the answer code surrounded by bracket (`[Qcode.type.answer]`).

* To show average of a question :
  * `[Qcode.mean]` give the average
  * `[Qcode.mean0]` give the average in integer
  * `[Qcode.mean2]` give the average rounded with 2 decimals
  * `[Qcode.moyenne]` give the average
  * `[Qcode.moy]` give the average in integer
  * `[Qcode.moy2]` give the average rounded with 2 decimals
* To show percentage : 
  * `[Qcode.percent]` : show the percentage with the same answer of respondant, between 0 and 100 with all decimals
  * `[Qcode.pourcent]` : show the percentage with the same answer of respondant, between 0 and 100 with all decimals
  * `[Qcode.pc]` : show the percentage with the same answer but between 0 and 100 without decimals
  * `[Qcode.percent.A1]` : show percentage with the answer A1
  * `[Qcode.pourcent.A1]` : show percentage with the answer A1
* To shown number of answer
  * `[Qcode.nb]` : show the total number of answers
  * `[Qcode.nbnum]` : show the total numeric number of answers
  * `[Qcode.nb.A1]` : show the number of answer A1 to question Q

You can use this variable in javascript workaround.

### Usage in Expression Manager ###

To use this number in Expression manager : you must use the quote : for example `{if(('[Qcode.nb.A1]'-100)>0,"There are "+('[Qcode.nb.A1]'-100)+" before this answer is quota out","This answer is quota out")}` .

### Specific restrictions ###

- You can not use this system in expression manager for _answer part_ (answers or subquestion) in LimeSurvey 3 and up;
- Relevance equation can not use this replacement;
- No update was done when survey is not activated.

## Installation

This plugin is tested with LimeSurvey 2.06, 2.65, 3.15.5, 3.26.3 and 5.2.8

### Via GIT
- Go to your LimeSurvey Directory (version up to 2.06 only)
- Clone in plugins/exportFilter directory `git clone https://git.framasoft.org/SondagePro-LimeSurvey-plugin/getStatInSurvey.git getStatInSurvey`

### Via ZIP dowload
- Download <http://extensions.sondages.pro/IMG/auto/getStatInSurvey.zip>
- Extract : `unzip getStatInSurvey.zip`
- Move the directory to  plugins/ directory inside LimeSUrvey

## Copyright
- Copyright © 2015-2021 Denis Chenu <https://sondages.pro>
- Copyright © 2021 Limesurvey GMBH <https://limesurvey.org>
- Copyright © 2015-2016 DareDo SA <http://www.daredo.net/>
- Copyright © 2016 Update France - Terrain d'études <http://www.updatefrance.fr/>
- Licence : GNU General Public License <https://www.gnu.org/licenses/gpl-3.0.html>
