# getStatInSurvey

Show statistics from previous answers in survey question text, help and answers.


## Documentation

You can show statistics from these question types:
* short text
* single choice (dropdown, radio, 5 point, Gender …)
* numeric
* equation

You use the code of question (Qcode), the statistic you want and, optionally, the answer code surrounded by brackets (`[Qcode.type.answer]`).

* To show the average of a question :
  * `[Qcode.mean]` gives the average
  * `[Qcode.mean0]` gives the average as an integer
  * `[Qcode.mean2]` gives the average rounded to 2 decimals
  * `[Qcode.moyenne]` gives the average
  * `[Qcode.moy]` gives the average as an integer
  * `[Qcode.moy2]` gives the average rounded to 2 decimals
* To show the percentage :
  * `[Qcode.percent]` : shows the percentage with the same answer as the respondent, between 0 and 100 with all decimals
  * `[Qcode.pourcent]` : shows the percentage with the same answer as the respondent, between 0 and 100 with all decimals
  * `[Qcode.pc]` : shows the percentage with the same answer as the respondent, but between 0 and 100 without decimals
  * `[Qcode.percent.A1]` : shows percentage with the answer A1
  * `[Qcode.pourcent.A1]` : shows percentage with the answer A1
* To show the number of answers
  * `[Qcode.nb]` : shows the total number of answers
  * `[Qcode.nbnum]` : shows the total numeric number of answers
  * `[Qcode.nb.A1]` : shows the number of answer A1 to question Qcode
* To show the answer code with the least filled responses
  * `[Qcode.leastfilled]` : shows the answer code that has the least filled responses (see docs/limesurvey_getstatinsurvey_leastfilled_example.lsa for example usage)


You can use these variables in a javascript workaround, or in setting a default answer to a question.

### Usage in Expression Manager ###

To use this number in Expression manager : you must use quotes : for example `{if(('[Qcode.nb.A1]'-100)>0,"There are "+('[Qcode.nb.A1]'-100)+" before this answer is quota out","This answer is quota out")}` .

### Specific restrictions ###

- You can not use this system in expression manager for the _answer part_ (answers or subquestion) in LimeSurvey 3 and up;
- Relevance equations can not use this replacement;
- Nothings is shown when the survey is not activated.

## Installation

This plugin is tested with LimeSurvey 2.06, 2.65, 3.15.5, 3.26.3 and 5.2.8

### Via GIT
- Go to your LimeSurvey Directory (version up to 2.06 only)
- Clone in plugins directory `git clone https://git.framasoft.org/SondagePro-LimeSurvey-plugin/getStatInSurvey.git getStatInSurvey`

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
