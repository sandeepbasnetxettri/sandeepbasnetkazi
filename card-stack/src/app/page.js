"use client";
import React, { useState } from 'react';
import { Swiper, SwiperSlide } from 'swiper/react';

import 'swiper/css';
import 'swiper/css/effect-cards';

import './style.css';

import { EffectCards } from 'swiper/modules';
import Image from 'next/image';

export default function App() {
  const [audio, setAudio] = useState(null);
  const pickupLines = [
    "Are you a magician? Because whenever I look at you, everyone else disappears.",
    "Do you have a name, or can I call you mine?",
    "Is your name Google? Because you have everything I’ve been searching for.",
    "Are you French? Because Eiffel for you.",
    "Is your dad a boxer? Because you're a knockout!",
    "Are you made of copper and tellurium? Because you're Cu-Te."
  ];

  const songs = [
    '/songs/song1.mp3', // Song for Image 1
    '/songs/song2.mp3', // Song for Image 2
    '/songs/song3.mp3', // Song for Image 3
    '/songs/song4.mp3', // Song for Image 4
    '/songs/song5.mp3', // Song for Image 5
    '/songs/song6.mp3', // Song for Image 6
  ];

  const playSong = (index) => {
    if (audio) {
      audio.pause();  // Stop the currently playing song
    }

    const newAudio = new Audio(songs[index]);
    newAudio.play();
    setAudio(newAudio);  // Set the new song to play
  };

  return (
    <>
      <div className='flex justify-center items-center min-h-screen'>
        <Swiper
          effect={'cards'}
          grabCursor={true}
          modules={[EffectCards]}
          className="mySwiper"
          onSlideChange={(swiper) => playSong(swiper.activeIndex)}  // Trigger song change on slide change
        >
          {pickupLines.map((line, index) => (
            <SwiperSlide key={index}>
              <div className="relative h-full w-full">
                <Image 
                  src={`/image.jpg`} 
                  alt={`Image ${index + 1}`} 
                  width={200} 
                  height={200} 
                  className="h-full w-full object-cover transition-all transform hover:scale-105" // Add hover effect for zoom-in
                />
                <div className="absolute bottom-0 left-0 right-0 p-4 bg-black bg-opacity-50 text-white text-lg opacity-0 transition-opacity duration-500 ease-in-out hover:opacity-100">
                  {line}
                </div>
                <div className="absolute inset-0 flex justify-center items-center">
                  <div className="animate-ping absolute h-4 w-4 bg-red-500 rounded-full opacity-75"></div>
                </div>
              </div>
            </SwiperSlide>
          ))}
        </Swiper>
      </div>
    </>
  );
}
