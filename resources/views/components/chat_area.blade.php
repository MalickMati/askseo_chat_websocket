<div class="chat-area">
    <div class="chat-header">
        <button class="hamburger" id="hamburger">
            <svg width="20" height="20" fill="#54656F" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M2 3h12a1 1 0 0 1 0 2H2a1 1 0 1 1 0-2m0 4h12a1 1 0 0 1 0 2H2a1 1 0 1 1 0-2m0 4h12a1 1 0 0 1 0 2H2a1 1 0 0 1 0-2" />
            </svg>
        </button>
        <div class="chat-header-info">
            <img src="" alt="" class="user-avatar">
            <div style="margin-left: 15px;">
                <div class="chat-name"></div>
                <div style="font-size: 13px; color: #667781;"></div>
            </div>
        </div>
        <div class="chat-header-actions">
            <svg id="chatMenuButton" style="cursor:pointer;" width="20" height="20" fill="#54656F"
                viewBox="0 0 256 256">
                <path
                    d="M156 128a28 28 0 1 1-28-28 28.03 28.03 0 0 1 28 28m-28-52a28 28 0 1 0-28-28 28.03 28.03 0 0 0 28 28m0 104a28 28 0 1 0 28 28 28.03 28.03 0 0 0-28-28" />
            </svg>
        </div>
    </div>

    <div class="messages-container" id="messagesContainer">

    </div>

    <div class="input-area" id="inputarea">
        <div id="filePreviewContainer" class="file-preview-container"></div>
        <label for="fileInput">
            <svg style="cursor: pointer;" width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path fill-rule="evenodd" clip-rule="evenodd"
                    d="M17.366 4.705a2.75 2.75 0 0 0-3.89 0l-8.131 8.132a4.25 4.25 0 1 0 6.01 6.01l8.762-8.762a.75.75 0 1 1 1.06 1.061l-8.761 8.762a5.75 5.75 0 1 1-8.132-8.132l8.132-8.131a4.25 4.25 0 1 1 6.01 6.01l-7.793 7.794a2.75 2.75 0 0 1-3.89-3.89l7.195-7.193a.75.75 0 1 1 1.06 1.06L7.804 14.62a1.25 1.25 0 0 0 1.768 1.768l7.794-7.794a2.75 2.75 0 0 0 0-3.889"
                    fill="#546573" />
            </svg>
        </label>
        <input type="file" id="fileInput" name="file" style="display: none;" />

        <div class="input-container">
            <i class="emoji-btn" id="emojiBtn"><svg width="20" height="20" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <g fill="none">
                        <path
                            d="M24 0v24H0V0zM12.593 23.258l-.011.002-.071.035-.02.004-.014-.004-.071-.035q-.016-.005-.024.005l-.004.01-.017.428.005.02.01.013.104.074.015.004.012-.004.104-.074.012-.016.004-.017-.017-.427q-.004-.016-.017-.018m.265-.113-.013.002-.185.093-.01.01-.003.011.018.43.005.012.008.007.201.093q.019.005.029-.008l.004-.014-.034-.614q-.005-.018-.02-.022m-.715.002a.02.02 0 0 0-.027.006l-.006.014-.034.614q.001.018.017.024l.015-.002.201-.093.01-.008.004-.011.017-.43-.003-.012-.01-.01z" />
                        <path
                            d="M12 2c5.523 0 10 4.477 10 10s-4.477 10-10 10S2 17.523 2 12 6.477 2 12 2m0 2a8 8 0 1 0 0 16 8 8 0 0 0 0-16m2.8 9.857a1 1 0 1 1 1.4 1.428A5.98 5.98 0 0 1 12 17a5.98 5.98 0 0 1-4.2-1.715 1 1 0 0 1 1.4-1.428A3.98 3.98 0 0 0 12 15c1.09 0 2.077-.435 2.8-1.143M8.5 8a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3m7 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3"
                            fill="#546573" />
                    </g>
                </svg></i>
            <textarea rows="1" id="messageInput" placeholder="Type a message..." maxlength="50000"></textarea>
        </div>

        <div id="emojiTooltip" class="emoji-tooltip">
            <!-- Emoji buttons -->
            <button class="emoji" data-emoji="😀">😀</button>
            <button class="emoji" data-emoji="😃">😃</button>
            <button class="emoji" data-emoji="😄">😄</button>
            <button class="emoji" data-emoji="😁">😁</button>
            <button class="emoji" data-emoji="😆">😆</button>
            <button class="emoji" data-emoji="🥹">🥹</button>
            <button class="emoji" data-emoji="😅">😅</button>
            <button class="emoji" data-emoji="😂">😂</button>
            <button class="emoji" data-emoji="🤣">🤣</button>
            <button class="emoji" data-emoji="🥲">🥲</button>
            <button class="emoji" data-emoji="☺️">☺️</button>
            <button class="emoji" data-emoji="😊">😊</button>
            <button class="emoji" data-emoji="😇">😇</button>
            <button class="emoji" data-emoji="🙂">🙂</button>
            <button class="emoji" data-emoji="🙃">🙃</button>
            <button class="emoji" data-emoji="😉">😉</button>
            <button class="emoji" data-emoji="😌">😌</button>
            <button class="emoji" data-emoji="😍">😍</button>
            <button class="emoji" data-emoji="🥰">🥰</button>
            <button class="emoji" data-emoji="😘">😘</button>
            <button class="emoji" data-emoji="😗">😗</button>
            <button class="emoji" data-emoji="😙">😙</button>
            <button class="emoji" data-emoji="😚">😚</button>
            <button class="emoji" data-emoji="😋">😋</button>
            <button class="emoji" data-emoji="😛">😛</button>
            <button class="emoji" data-emoji="😝">😝</button>
            <button class="emoji" data-emoji="😜">😜</button>
            <button class="emoji" data-emoji="🤪">🤪</button>
            <button class="emoji" data-emoji="🤨">🤨</button>
            <button class="emoji" data-emoji="🧐">🧐</button>
            <button class="emoji" data-emoji="🤓">🤓</button>
            <button class="emoji" data-emoji="😎">😎</button>
            <button class="emoji" data-emoji="🥸">🥸</button>
            <button class="emoji" data-emoji="🤩">🤩</button>
            <button class="emoji" data-emoji="🥳">🥳</button>
            <button class="emoji" data-emoji="🙂‍↕️">🙂‍↕️</button>
            <button class="emoji" data-emoji="😫">😫</button>
            <button class="emoji" data-emoji="😖">😖</button>
            <button class="emoji" data-emoji="😣">😣</button>
            <button class="emoji" data-emoji="☹️">☹️</button>
            <button class="emoji" data-emoji="🙁">🙁</button>
            <button class="emoji" data-emoji="😕">😕</button>
            <button class="emoji" data-emoji="😟">😟</button>
            <button class="emoji" data-emoji="😔">😔</button>
            <button class="emoji" data-emoji="😞">😞</button>
            <button class="emoji" data-emoji="🙂‍↔️">🙂‍↔️</button>
            <button class="emoji" data-emoji="😒">😒</button>
            <button class="emoji" data-emoji="😏">😏</button>
            <button class="emoji" data-emoji="😩">😩</button>
            <button class="emoji" data-emoji="🥺">🥺</button>
            <button class="emoji" data-emoji="😢">😢</button>
            <button class="emoji" data-emoji="😭">😭</button>
            <button class="emoji" data-emoji="😤">😤</button>
            <button class="emoji" data-emoji="😠">😠</button>
            <button class="emoji" data-emoji="😡">😡</button>
            <button class="emoji" data-emoji="🤬">🤬</button>
            <button class="emoji" data-emoji="🤯">🤯</button>
            <button class="emoji" data-emoji="😳">😳</button>
            <button class="emoji" data-emoji="🥵">🥵</button>
            <button class="emoji" data-emoji="🥶">🥶</button>
            <button class="emoji" data-emoji="🫡">🫡</button>
            <button class="emoji" data-emoji="🫢">🫢</button>
            <button class="emoji" data-emoji="🤭">🤭</button>
            <button class="emoji" data-emoji="🫣">🫣</button>
            <button class="emoji" data-emoji="🤔">🤔</button>
            <button class="emoji" data-emoji="🤗">🤗</button>
            <button class="emoji" data-emoji="😓">😓</button>
            <button class="emoji" data-emoji="😥">😥</button>
            <button class="emoji" data-emoji="😰">😰</button>
            <button class="emoji" data-emoji="😨">😨</button>
            <button class="emoji" data-emoji="😱">😱</button>
            <button class="emoji" data-emoji="🤫">🤫</button>
            <button class="emoji" data-emoji="🫠">🫠</button>
            <button class="emoji" data-emoji="🤥">🤥</button>
            <button class="emoji" data-emoji="😶">😶</button>
            <button class="emoji" data-emoji="🫥">🫥</button>
            <button class="emoji" data-emoji="😐">😐</button>
            <button class="emoji" data-emoji="🫤">🫤</button>
            <button class="emoji" data-emoji="😑">😑</button>
            <button class="emoji" data-emoji="🫨">🫨</button>
            <button class="emoji" data-emoji="😬">😬</button>
            <button class="emoji" data-emoji="🙄">🙄</button>
            <button class="emoji" data-emoji="😯">😯</button>
            <button class="emoji" data-emoji="🤐">🤐</button>
            <button class="emoji" data-emoji="😵‍💫">😵‍💫</button>
            <button class="emoji" data-emoji="😵">😵</button>
            <button class="emoji" data-emoji="😮‍💨">😮‍💨</button>
            <button class="emoji" data-emoji="😪">😪</button>
            <button class="emoji" data-emoji="🤤">🤤</button>
            <button class="emoji" data-emoji="😴">😴</button>
            <button class="emoji" data-emoji="🥱">🥱</button>
            <button class="emoji" data-emoji="😲">😲</button>
            <button class="emoji" data-emoji="😮">😮</button>
            <button class="emoji" data-emoji="😧">😧</button>
            <button class="emoji" data-emoji="😦">😦</button>
            <button class="emoji" data-emoji="🥴">🥴</button>
            <button class="emoji" data-emoji="🤢">🤢</button>
            <button class="emoji" data-emoji="🤮">🤮</button>
            <button class="emoji" data-emoji="🤧">🤧</button>
            <button class="emoji" data-emoji="😷">😷</button>
            <button class="emoji" data-emoji="🤒">🤒</button>
            <button class="emoji" data-emoji="🤕">🤕</button>
            <button class="emoji" data-emoji="🤑">🤑</button>
        </div>

        <button class="send-button" id="sendMessageBtn">
            <svg width="20" height="20" viewBox="0 0 15 15" fill="none">
                <path
                    d="M14.954.71a.5.5 0 0 1-.1.144L5.4 10.306l2.67 4.451a.5.5 0 0 0 .889-.06zM4.694 9.6.243 6.928a.5.5 0 0 1 .06-.889L14.293.045a.5.5 0 0 0-.146.101z"
                    fill="#fff" />
            </svg>
        </button>
    </div>

</div>