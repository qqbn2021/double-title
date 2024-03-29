=== 双标题 ===
Contributors: wyzda2021
Donate link: https://www.ggdoc.cn
Tags:双标题, 副标题, 标题, 自动设置, 文章双标题, double title
Requires at least: 5.0
Requires PHP:5.3
Tested up to: 6.0
Stable tag: 0.0.4
License: GNU General Public License v2.0 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

支持文章双标题显示、自定义双标题显示模板、自动解析已是双标题的文章。

== Description ==

支持文章双标题显示、自定义双标题显示模板、自动解析已是双标题的文章。

如果使用双标题后效果不是很好，只需要删除插件或停用插件，就可以恢复到未使用插件之前的效果（因为本插件没有修改文章表原始内容）。

1、支持设置双标题模板，按照自己的方式显示文章双标题。

2、支持自动解析已是双标题的文章，双标题格式转换就是这么简单。

3、支持预生成双标题，再也不怕网站加载慢。

4、无需复杂设置，一键使用双标题。

5、支持删除已生成的双标题，不满意就重新生成。

6、支持修改title标签内容为双标题，让搜索引擎抓取到文章的双标题。同时还支持是否在文章列表或详情页面显示双标题，保持页面文章可读性。

7、支持所有流行的搜索引擎数据来源，提升双标题匹配成功率。（商业版）

== Installation ==

1. 进入WordPress网站后台，找到“插件-安装插件”菜单；
2. 点击界面左上方的“上传插件”按钮，选择本地提前下载好的插件压缩包文件（zip格式），点击“立即安装”；
3. 安装完成后，启用 “双标题” 插件；
4. 通过“设置”链接进入插件设置界面；
5. 完成设置后，插件就安装完毕了。

== Frequently Asked Questions ==

= 使用双标题后有什么效果？ =
据某站长论坛用户帖子，使用双标题后流量大增，同时还提升了网站的权重。本插件对此效果持保留态度，有没有效果，只有自己用了才会知道。

= 为什么文章显示变慢了？ =
如果您的文章没有生成双标题，插件就会自动生成双标题，此过程需要从接口获取数据，所以会变慢。当文章已生成了双标题，则会很快。

= 旧文章本来就是双标题，可以导入吗？ =
如果您的旧文章本来就是双标题，可以设置文章标题生成模板，插件会按照这个设置解析双标题，解析成功后，就会按照文章标题显示模板显示双标题。

= 能否仅在标签栏显示双标题，而页面不显示双标题？ =
可以的，只需要在设置中将“启用页面上的双标题”设置为否，将“启用标签栏上的双标题”设置为是即可。

= 我设置了在文章列表、详情页面不显示双标题，为什么还是显示双标题了？ =
如果文章标题原来就是双标题，则不管您设置如何，它都会显示双标题，插件不会对原文章内容处理的。您可以手动修改原文章标题。

= 为什么某些内容无法使用双标题？ =
目前插件只会处理文章类型的内容，其它类型的内容无法处理，例如：页面。

= 为什么双标题插件无法正常使用？ =
如果您装的其它插件或者主题对标题也进行了处理，则双标题插件很有可能无法正常运行。

= 商业版有哪些功能？ =
商业版的数据来源更全，包含了所有目前流行的搜索引擎数据。如果过度使用单一数据来源，您的IP可能会被拦截，导致无法生成双标题，强烈建议使用商业版。

= 联系作者 =
如果插件使用出现了问题，或者想要定制功能，可以加QQ：1445023846。


== Screenshots ==

1. 双标题设置
2. 双标题设置
3. 前台文章列表页面显示双标题
4. 前台文章详情页面显示双标题
5. HTML标签内显示双标题
6. 后台文章编辑或发布页面不显示双标题

== Upgrade Notice ==

= 0.0.4 =
* 新增分类设置，设置指定分类下的文章使用双标题
* 新增预生成文章双标题、删除文章双标题功能
* 自动解析文章标题显示模板格式的文章
* 去掉了后台文章列表显示双标题重置功能

= 0.0.3 =
* 新增文章生成模板，已有双标题文章轻松使用本插件
* 新增预生成双标题设置，增加双标题成功率
* 新增数据来源设置，选择自己喜欢的搜索引擎数据
* 新增后台文章列表显示双标题重置按钮，重置双标题就是这么简单

= 0.0.2 =
新增自定义双标题接口设置

= 0.0.1 =
参考Changelog说明

== Changelog ==

= 0.0.4 =
* 新增分类设置，设置指定分类下的文章使用双标题
* 新增预生成文章双标题、删除文章双标题功能
* 自动解析文章标题显示模板格式的文章
* 去掉了后台文章列表显示双标题重置功能

= 0.0.3 =
* 新增文章生成模板，已有双标题文章轻松使用本插件
* 新增预生成双标题设置，增加双标题成功率
* 新增数据来源设置，选择自己喜欢的搜索引擎数据
* 新增后台文章列表显示双标题重置按钮，重置双标题就是这么简单

= 0.0.2 =
* 新增自定义双标题接口设置

= 0.0.1 =
* 新增双标题列表，可以查看原标题与副标题的内容
* 新增编辑和删除功能，副标题不好，只需要手动编辑或删除即可
* 新增同步标题功能，一键为文章设置副标题
* 新增同步文章功能，可将旧文章同步过来
* 新增双标题设置功能，可自定义双标题模板显示