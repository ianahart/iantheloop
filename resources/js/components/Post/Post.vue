<template>
    <div v-if="postsLoaded" :data-id="post.id" ref="post" class="post__container">
        <FlaggedOptions
            :post="post"
            @flagpost="handleFlagPost"
        />
        <div class="post_top__container">
            <div class="post_top__column">
                <div class="post_author_profile_pic__container">
                    <img
                        v-if="post.profile_picture"
                        :src="post.profile_picture"
                    />
                    <DefaultProfileIcon
                        v-else
                        className="default_profile_image_rel_sm"
                    />
                </div>
                <div class="post_header__container">
                    <div class="post__names">
                        <router-link
                            :to="{
                                name: 'Profile',
                                params: {
                                    id: `${post.author_user_id.toString()}`
                                }
                            }"
                        >
                        <p>{{ post.full_name }}</p>
                        </router-link>
                        <div v-if="postsOrigin.toLowerCase() === 'profile'" class="post_subject_name">
                            <PlaySolidIcon className="icon__xsm__dark" />
                            <p>{{ post.subject_name }}</p>
                        </div>
                    </div>
                    <div class="posted__date">
                        <p>{{ post.post_posted }}</p>
                    </div>
                </div>
            </div>
            <div
                @click="togglePostOptions"
                class="post_options_dots__container"
            >
                <PostActions
                    v-if="isPostOptionsOpen"
                    :authorUserId="post.author_user_id"
                    :subjectUserId="post.subject_user_id"
                    :postId="post.id"
                />
                <div ref="postActionsTrigger">
                    <HorizontalDotsIcon
                        className="horizontal_dots__icon"
                        marker="postActions"
                    />
                </div>
            </div>
        </div>
        <div class="post_body__container_row">
            <p class="post_body__text">{{ post.post_text }}</p>
            <div
                v-if="post.photo_link !== null || post.video_link !== null"
                class="post_body__media_container"
            >
                <a v-if="post.photo_link !== null" :href="post.photo_link">
                    <img
                        v-if="post.photo_link !== null"
                        :src="post.photo_link"
                        :alt="post.photo_filename"
                    />
                </a>
                <div
                    v-if="post.video_link !== null"
                    class="post_body__media_player"
                >
                    <a v-if="post.video_link !== null" :href="post.video_link">
                        <VideoPlayer :src="post.video_link" />
                    </a>
                </div>
            </div>
        </div>
        <div class="post__stats_count">
            <div class="post__likes_count">
                <div class="post_likes_count_icon__container">
                    <ThumbsUpIcon />
                </div>
                <div v-if="getUserId" class="post_likes_names">
                     <span v-if="post.likes > 0">{{ postLikesText }}</span>
                </div>
            </div>
            <div class="post__comments_count">
                <span>{{ post.comments_count }}</span>
                <span>comments</span>
            </div>
        </div>
        <div class="post__divider"></div>
        <div class="post__interactions_container">
            <Likes
                @addlike="handleAddLike"
                @removelike="handleRemoveLike"
                :data="post"
                :currentUserId="getUserId"
            />
            <CommentTrigger
                @toggle="toggleCommentForm"
            />
        </div>
        <div class="post__divider"></div>
        <div class="post_comment_row">
            <CommentForm
                :post="post"
                :currentUserProfilePic="currentUserProfilePic"
                :isCommentFormOpen="isCommentFormOpen"
                :userId="getUserId"
                @closeform="closeCommentForm"
            />
        </div>
        <div class="post_comment_row">
            <div v-if="post.post_comments.length && commentPreviewShowing">
                <p class="toggle_comments_btn" @click="toggleComments">View more comments...</p>
                <Comment
                  :postComment="previewComment"
                  :postAuthorUserId="post.author_user_id"
                />
            </div>
            <Comments
              v-else-if="post.post_comments.length && !commentPreviewShowing"
              :post="post"
            />
        </div>
        <div class="md_spacer">
            <p
              @click="loadMoreComments"
              class="load_more_post_comments"
              v-if="!commentPreviewShowing && post.post_comments.length <= post.comments_count && commentsLoaded.postId !== post.id"
            >
              Load More...
            </p>
            <p
                class="post_comments_loaded"
                v-if="post.comments_count === post.post_comments.length && post.id === commentsLoaded.postId"
            >
              {{ commentsLoaded.message }}
            </p>
        </div>
    </div>
</template>

<script>
import { mapState, mapGetters, mapMutations, mapActions } from "vuex";

import HorizontalDotsIcon from "../Icons/HorizontalDotsIcon.vue";
import PlaySolidIcon from "../Icons/PlaySolidIcon.vue";
import VideoPlayer from "../ProfileWall/VideoPlayer.vue";
import DefaultProfileIcon from "../Icons/DefaultProfileIcon.vue";
import PostActions from "./PostActions.vue";
import Likes from "./Likes.vue";
import ThumbsUpIcon from "../Icons/ThumbsUpIcon.vue";
import CommentTrigger from "../Comment/CommentTrigger.vue";
import FlaggedOptions from './FlaggedOptions.vue';
import CommentForm from '../Comment/CommentForm.vue';
import Comments from '../Comment/Comments.vue';
import Comment from '../Comment/Comment.vue';


export default {
    name: "Post",

    props: {
        lastPostItem: Number,
        post: Object,
        observer: IntersectionObserver,
        currentUserProfilePic: String,
        postsOrigin: String,
    },

    components: {
        HorizontalDotsIcon,
        PlaySolidIcon,
        VideoPlayer,
        DefaultProfileIcon,
        PostActions,
        Likes,
        ThumbsUpIcon,
        CommentTrigger,
        FlaggedOptions,
        CommentForm,
        Comments,
        Comment,
    },

    data() {
     return {
            isPostOptionsOpen: false,
            isCommentFormOpen: false,
            debounceID: "",
            errorID: '',
            commentPreviewShowing: true,
        };
    },

    mounted() {

        this.$refs.post.addEventListener("click", this.closeOptionsFallback);
        this.$nextTick(() => {
            if (this.lastPostItem === this.post.id) {
                if (this.$refs.post) {
                    this.observer.observe(this.$refs.post);
                }
            }
        });
    },

    watch: {
        "post.seen": function() {
            if (this.post.seen) {
                this.observer.unobserve(this.$refs.post);
            }
        },
    },

    beforeDestroy() {
        this.$refs.post.removeEventListener("click", this.closeOptionsFallback);
        clearTimeout(this.debounceID);
        clearTimeout(this.errorID);
    },

    computed: {

        ...mapState('posts',
            [
                'alreadyFlaggedError',
                'flagPostFinished',
                'commentsLoaded',
                'postsLoaded'
            ]
        ),

        ...mapGetters("user",
            [
                "getUserId"
            ]
        ),

        previewComment() {


            return this.post.post_comments.length ? this.post.post_comments.slice(0, 1)[0] : [];
        },

        postLikesText () {

            let text = '';
            let likes = this.post.post_likes;
            const currentUser = likes.find((user) => user.user_id === this.getUserId);
            let names;

            if (likes.length === 1) {
                const dynamicSubStr = currentUser ? `You` : `${likes[0].liker_name}`;
                text = `${dynamicSubStr} liked this`;
            }

            if (likes.length === 2) {
                names = currentUser ? likes.find((lk) => lk.user_id !== this.getUserId) : likes.map((lk) => lk.liker_name).join(' and ');
                text = `${currentUser ? 'You and' : ''} ${typeof names === 'string' ? names : names.liker_name} liked this`;
            }

            if (likes.length > 2) {
                let offset = currentUser ? 1 : 2;

                names = likes.slice(likes.length - offset).map((lk, index) => {
                    if (lk.user_id === this.getUserId) {
                       const { liker_name} = likes.slice(-2, -1)[0];
                        return liker_name;
                    } else {
                        return lk.liker_name;
                    }
                });

                if (currentUser) {
                    names.unshift('You, ');
                    const idx = likes.findIndex((lk) => currentUser.user_id === lk.user_id);
                    offset = 2;
                }
                names = names.join(' ');
                text =  `${names} and ${likes.length - offset} others like this`;
            }
            return text;
        },
    },

    methods: {

        ...mapMutations('posts',
            [
                'CLOSE_MODAL',
                'CLEAR_ALREADY_FLAGGED_ERROR',
                'RESET_FLAGGED_OPTIONS',
                'FLAG_POST_FINISHED'
            ]
        ),

        ...mapActions("posts",
            [
                "LIKE_POST",
                'UNLIKE_POST',
                'FLAG_POST',
                'REFILL_COMMENTS',
            ]
        ),

        toggleComments() {
            this.commentPreviewShowing = false;
        },

        closeCommentForm(payload) {
            this.isCommentFormOpen = payload;
        },

        togglePostOptions() {
            this.isPostOptionsOpen = !this.isPostOptionsOpen;
        },

        closeOptionsFallback(e) {
            if (e.target.id !== "postActions") {
                this.isPostOptionsOpen = false;
            }
        },

        toggleCommentForm() {
            this.isCommentFormOpen = !this.isCommentFormOpen;
        },

        loadMoreComments() {
          this.debounce(async() => {
              await this.REFILL_COMMENTS(this.post.id);
          }, 400);
        },

        async handleAddLike(payload) {

            this.debounce(async () => {
                await this.LIKE_POST({
                    postId: payload,
                    currentUserId: this.getUserId
                });
            }, 350);
        },

        async handleRemoveLike(payload) {

            this.debounce(async () => {
                await this.UNLIKE_POST(payload);
            }, 350);
        },

        async handleFlagPost (payload) {

            this.debounce(async () => {
                    await this.FLAG_POST({ postId: payload, userId: this.getUserId });

                    if (this.flagPostFinished) {
                        if (this.alreadyFlaggedError.length) {

                            this.errorID = setTimeout(() => {
                                this.CLEAR_ALREADY_FLAGGED_ERROR();
                                this.RESET_FLAGGED_OPTIONS();
                                this.FLAG_POST_FINISHED(false);
                                this.CLOSE_MODAL();
                        }, 2500);
                        } else {
                            this.RESET_FLAGGED_OPTIONS();
                            this.FLAG_POST_FINISHED(false);
                            this.CLOSE_MODAL();
                        }
                    }
            }, 400);
        },

        debounce(fn, delay = 400) {
            return ((...args) => {
                clearTimeout(this.debounceID);

                this.debounceID = setTimeout(() => {
                    this.debounceID = null;

                    fn(...args);
                }, delay);
            })();
        }
    }
};
</script>

<style lang="scss">
.post__container {
    background-color: $primaryGray;
    border: 1px solid $primaryGray;
    padding: 0.5rem;
    width: 80%;
    margin: 0 auto;
    border-radius: 8px;
    box-shadow: rgb(0 0 0 / 10%) 0px 20px 25px -5px,
        rgb(0 0 0 / 4%) 0px 10px 10px -5px;
    margin-bottom: 1.5rem;
    box-sizing: border-box;
    position: relative;
}

.post_top__container {
    box-sizing: border-box;
    display: flex;
    justify-content: space-between;
}

.post_top__column {
    display: flex;
}

.post_likes_names {
  display: flex;
  align-items: center;
}

.post__names {
    display: flex;
    align-items: center;

    a {
        cursor: pointer;
        text-decoration: none;
    }

    p {
        margin: 1rem 0.3rem 0 0.3rem;
        font-family: "Secular One", sans-serif;
        font-weight: 200;
        color: lighten($primaryBlack, 5);
    }

    svg {
        margin: 1rem 0.3rem 0 0.3rem;
        color: darken($primaryGray, 15);
    }
}

.post_author_profile_pic__container {
    img {
        width: 45px;
        height: 45px;
        border-radius: 50%;
    }

    svg {
        color: $themePink;
        background: $themeLightBlue;
    }
}

.posted__date {
    p {
        font-size: 0.8rem;
        color: gray;
        margin-top: 0.4rem;
    }
}

.post_body__container_row {
    margin-top: 1.2rem;
    box-sizing: border-box;
}

.post_body__text {
    font-size: 0.9rem;
    color: lighten($primaryBlack, 4);
    font-family: "Open Sans", sans-serif;
    line-height: 1.6;
}

.post__divider {
    border-top: 1px solid $mainInputBorder;
    width: 100%;
    margin: 0.5rem 0;
}

.post_body__media_container {
    display: flex;
    justify-content: center;

    img {
        width: 200px;
        height: 200px;
        border-radius: 8px;
    }
}

.post_body__media_player {
    width: 200px;
    height: 200px;
    border-radius: 8px;
}

.post_subject_name {
    display:flex;
    align-items: center;
    overflow-wrap: break-word;
    word-wrap: break-word;
    -webkit-hyphens: auto;
    -ms-hyphens: auto;
    -moz-hyphens: auto;
    hyphens: auto;
}

.post_options_dots__container {
    position: relative;
}

.post__stats_count {
    margin: 0.3rem 0;
    display: flex;
    justify-content: space-between;
    box-sizing: border-box;
}

.post_likes_count_icon__container {
    height: 17px;
    width: 17px;
    display: flex;
    justify-content: center;
    align-items: center;
    border-radius: 50%;
    background-color: $themeLightBlue;
    margin-right: 0.1rem;

    svg {
        color: #fff;
        height: 12px;
        width: 12px;
    }
}

.post__likes_count {
    display: flex;
    align-items: center;
    box-sizing: border-box;
    span {
        margin: 0 0.1rem;
        font-size: 0.8rem;
        color: $mainInputLabel;
    }
}

.post__comments_count {
    display: flex;
    align-items: center;
    box-sizing: border-box;
    span {
        font-size: 0.8rem;
        margin: 0 0.1rem;
        color: $mainInputLabel;
    }
}

.post__interactions_container {
    display: flex;
    justify-content: space-evenly;
}

.post_comment_row {
    margin: 1rem auto 0.3rem auto;
}

.toggle_comments_btn {
    font-size: 0.9rem;
    color: gray;
    font-family: 'Open sans', sans-serif;
    cursor: pointer;
    transition: all 0.25s ease-out;

    &:hover {
        color: darken(gray, 5);
    }
}

.load_more_post_comments,
.post_comments_loaded {
    text-align: center;
    font-size: 0.9rem;
    transition: all 0.25s ease-out;
    font-family: 'Open Sans', sans-serif;
}

.load_more_post_comments {
    color: gray;
    cursor: pointer;
    &:hover {
      color: lighten(gray, 5);
    }
}

.post_comments_loaded {
    color: $themeLightBlue;
      &:hover {
      color: lighten($themeLightBlue, 5);
    }
}

@media (max-width: 600px) {
    .post__container {
        width: 100%;
    }
    .post_body__text {
        font-size: 0.7rem;
    }
    .post__names {
        p {
            font-size: 0.65rem;
        }
        svg {
            width: 14px;
            height: 14px;
        }
    }
    .post_subject_name {
        font-size: 0.65rem;
    }
}
</style>
