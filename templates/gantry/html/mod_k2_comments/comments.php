<?php
/**
 * @version		2.6.x
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2014 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die;

?>

<div id="reviews">
	<h1>Отзывы покупателей <a href="/reviews"><span>Смотреть все отзывы</span><i class="icon-all-reviews"></i></a></h1>
	<?php if(count($comments)): ?>
	<ul class="slides">
		<?php foreach ($comments as $key=>$comment):	?>
		<li class="<?php echo ($key%2) ? "odd" : "even"; if(count($comments)==$key+1) echo ' lastItem'; ?>">
			
			<i class="icon-reviews"></i>
			<div class="com-header">
				<?php if($params->get('commenterName')): ?>
				<span class="username">
					<?php if(isset($comment->userLink)): ?>
					<a rel="author" href="<?php echo $comment->userLink; ?>"><?php echo $comment->userName; ?></a>
					<?php elseif($comment->commentURL): ?>
					<a target="_blank" rel="nofollow" href="<?php echo $comment->commentURL; ?>"><?php echo $comment->userName; ?></a>
					<?php else: ?>
					<?php echo $comment->userName; ?>
					<?php endif; ?>
				</span>
				<?php endif; ?>
				
				<?php if($params->get('commentDate')): ?>
				<span class="commentDate">
					<?php echo JHTML::_('date', $comment->commentDate, JText::_('DATE_FORMAT_LC4')); ?>
				</span>
				<?php endif; ?>
			</div>

			<div class="comment"><?php echo $comment->commentText; ?></div>

		</li>
		<?php endforeach; ?>
	</ul>
	<?php endif; ?>

</div>
