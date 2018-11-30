# getStatInSurvey

Allow to show some statictics from previous answers in survey in question text, help and answers.


## Documentation

You can show statistics from this question type : single choice, numeric and equation. You use the code of question, the statistic you want and, optionnaly, the answer code surrounded by bracket (`[Qcode.type.answer]`).

* To show average of a question : use moyenne
  * `[Q.moyenne]` give the average
  * `[Q.moy]` give the average in integer
  * `[Q.moy2]` give the average rounded with 2 decimalq
* To show percentage : use pourcent
  * `[Q.pourcent]` : show the percentage with the same answer of respondant, betwwen 0 and 1
  * `[Q.pc]` : show the prercentage with the same answer but betwwen 0 and 100 without decimals
  * `[Q.pourcent.A1]` : show percentage with the answer A1
* To shown number of answer
  * `[Q.nb]` : show the total number of answers
  * `[Q.nbnum]` : show the total numeric number of answers
  * `[Q.nb.A1]` : show the number of answer A1 to question Q

You can use this variable in javascript workaround.

### Usage in Expression Manager ###

To use this number in Expression manager : you must use the quote : for example `{if(('[Q.nb.A1]'-100)>0,"There are "+('[Q.nb.A1]'-100)+" before this answer is quota out","This answer is quota out")}` .

**You can not use this system in expression manager for answer part in LimeSurvey 3 and up.**

**Relevance equation can not use this replacement**.

## Installation

This plugin is tested with LimeSurvey 2.06,2.65 and 3.15.5

### Via GIT
- Go to your LimeSurvey Directory (version up to 2.06 only)
- Clone in plugins/exportFilter directory `git clone https://git.framasoft.org/SondagePro-LimeSurvey-plugin/getStatInSurvey.git getStatInSurvey`

### Via ZIP dowload
- Download <http://extensions.sondages.pro/IMG/auto/getStatInSurvey.zip>
- Extract : `unzip getStatInSurvey.zip`
- Move the directory to  plugins/ directory inside LimeSUrvey

## Copyright
- Copyright © 2015-2018 Denis Chenu <http://sondages.pro>
- Copyright © 2015-2016 DareDo SA <http://www.daredo.net/>
- Copyright © 2016 Update France - Terrain d'études <http://www.updatefrance.fr/>
- Licence : GNU General Public License <https://www.gnu.org/licenses/gpl-3.0.html>
