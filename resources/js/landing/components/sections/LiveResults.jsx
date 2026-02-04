import React, { useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { MdHowToVote, MdPerson, MdLeaderboard } from 'react-icons/md';
import { HiQuestionMarkCircle, HiTrendingUp, HiStatusOnline } from 'react-icons/hi';

export default function LiveResults() {
    const [elections, setElections] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    const fetchResults = async () => {
        try {
            const response = await fetch('/api/live-results');
            const data = await response.json();
            if (data.success) {
                setElections(data.elections);
                setError(null);
            } else {
                setError('Failed to load results');
            }
        } catch (err) {
            console.error('Error fetching live results:', err);
            setError('Unable to connect to server');
        } finally {
            setLoading(false);
        }
    };

    // Initial fetch and polling every 5 seconds for real-time updates
    useEffect(() => {
        fetchResults();
        const interval = setInterval(fetchResults, 5000);
        return () => clearInterval(interval);
    }, []);

    const ongoingElections = elections.filter(e => e.status === 'ongoing');
    const completedElections = elections.filter(e => e.status === 'completed');
    const hasElections = elections.length > 0;

    // Placeholder bar heights for "Hourly Activity" when empty (like reference dashboard)
    const placeholderBars = [28, 42, 38, 55, 48, 65, 72, 88];

    return (
        <section id="live-results" className="py-16 sm:py-20 lg:py-24 relative overflow-hidden bg-white">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                {/* ONE permanent card – dark green like Real-Time Analytics Dashboard reference */}
                <motion.div
                    initial={{ opacity: 0, y: 20 }}
                    whileInView={{ opacity: 1, y: 0 }}
                    viewport={{ once: true }}
                    className="relative rounded-3xl overflow-hidden shadow-xl"
                    style={{
                        background: 'linear-gradient(135deg, #166534 0%, #14532d 50%, #052e16 100%)',
                    }}
                >
                    {/* Subtle dot texture */}
                    <div className="absolute inset-0 opacity-30" style={{
                        backgroundImage: `radial-gradient(circle at 1px 1px, rgba(255,255,255,0.08) 1px, transparent 0)`,
                        backgroundSize: '16px 16px',
                    }} />

                    {loading && (
                        <div className="relative flex flex-col items-center justify-center py-24">
                            <div className="w-12 h-12 border-2 border-white/20 border-t-gov-gold-400 rounded-full animate-spin mb-4" />
                            <p className="text-white/80 text-sm">Loading election results...</p>
                        </div>
                    )}

                    {error && !loading && (
                        <div className="relative text-center py-16 px-6">
                            <HiQuestionMarkCircle className="w-12 h-12 text-red-300 mx-auto mb-3" />
                            <p className="text-white/90">{error}</p>
                        </div>
                    )}

                    {/* Empty state – two-column layout like reference (dark green card, white text, yellow accents) */}
                    {!loading && !error && !hasElections && (
                        <div className="relative grid lg:grid-cols-2 gap-8 lg:gap-10 p-6 sm:p-8 lg:p-10 xl:p-12">
                            {/* Left: Premium-style badge, title, paragraph, bullet list */}
                            <div className="flex flex-col justify-center">
                                <span className="inline-flex w-fit items-center rounded-full bg-gov-gold-400 px-3 py-1 text-xs font-semibold text-gov-green-900 mb-4">
                                    Election Results
                                </span>
                                <h2 className="text-2xl sm:text-3xl lg:text-4xl font-bold text-white leading-tight mb-4">
                                    Recent Election
                                    <br />
                                    <span className="pl-0 lg:pl-2">Results</span>
                                </h2>
                                <p className="text-white/90 text-sm sm:text-base leading-relaxed mb-6 max-w-lg">
                                    Monitor election progress with our live results dashboard. When the admin publishes an election—ongoing or completed—results will appear here. Until then, this space is reserved for transparency.
                                </p>
                                <ul className="space-y-2 text-white/90 text-sm sm:text-base">
                                    {['Live participation metrics', 'Position-by-position breakdowns', 'Transparent vote counts', 'Admin-controlled visibility'].map((item, i) => (
                                        <li key={i} className="flex items-center gap-2">
                                            <span className="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full bg-gov-gold-400 flex-shrink-0" />
                                            {item}
                                        </li>
                                    ))}
                                </ul>
                            </div>
                            {/* Right: nested lighter-green panels (glass effect) – Total Votes, Turnout, Hourly Activity */}
                            <div className="grid grid-cols-2 gap-4 lg:gap-5 content-start">
                                <div className="rounded-xl bg-white/10 backdrop-blur-sm border border-white/10 p-4 sm:p-5">
                                    <p className="text-white/80 text-xs sm:text-sm mb-1">Total Votes</p>
                                    <p className="text-2xl sm:text-3xl font-bold text-white">—</p>
                                </div>
                                <div className="rounded-xl bg-white/10 backdrop-blur-sm border border-white/10 p-4 sm:p-5">
                                    <p className="text-white/80 text-xs sm:text-sm mb-1">Turnout</p>
                                    <p className="text-2xl sm:text-3xl font-bold text-gov-gold-400">—</p>
                                </div>
                                <div className="col-span-2 rounded-xl bg-white/10 backdrop-blur-sm border border-white/10 p-4 sm:p-5">
                                    <p className="text-white/80 text-xs sm:text-sm mb-3">Hourly Activity</p>
                                    <div className="flex items-end gap-1 sm:gap-2 h-24">
                                        {placeholderBars.map((h, i) => (
                                            <motion.div
                                                key={i}
                                                initial={{ height: 0 }}
                                                animate={{ height: `${h}%` }}
                                                transition={{ duration: 0.5, delay: i * 0.05 }}
                                                className="flex-1 rounded-t bg-gov-gold-400 min-h-[8px]"
                                            />
                                        ))}
                                    </div>
                                </div>
                            </div>
                        </div>
                    )}

                    {/* Elections content – when admin displays elections; same dark green card, inner panels like reference */}
                    {!loading && !error && hasElections && (
                    <div className="relative p-6 sm:p-8 lg:p-10 xl:p-12 space-y-8">
                        {/* Ongoing Elections First */}
                        {ongoingElections.map((election, electionIndex) => (
                            <motion.div
                                key={election.id}
                                initial={{ opacity: 0, y: 30 }}
                                whileInView={{ opacity: 1, y: 0 }}
                                viewport={{ once: true }}
                                transition={{ duration: 0.5, delay: electionIndex * 0.1 }}
                                className="bg-white/10 backdrop-blur-sm rounded-2xl p-5 sm:p-6 lg:p-8 border border-white/10 shadow-lg"
                            >
                                {/* Election Header */}
                                <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 pb-4 border-b border-white/10">
                                    <div>
                                        <div className="flex items-center gap-2 mb-2">
                                            <span className="inline-flex items-center gap-1.5 bg-green-500/20 border border-green-400/30 text-green-300 text-xs font-semibold px-2.5 py-1 rounded-full">
                                                <HiStatusOnline className="w-3 h-3 animate-pulse" />
                                                LIVE NOW
                                            </span>
                                        </div>
                                        <h3 className="text-xl sm:text-2xl font-bold text-white mb-1">
                                            {election.election_name}
                                        </h3>
                                        {election.organization && (
                                            <p className="text-white/70 text-sm">{election.organization}</p>
                                        )}
                                        {election.started_at && (
                                            <p className="text-white/60 text-xs mt-1">
                                                Started: {election.started_at}
                                            </p>
                                        )}
                                    </div>
                                    <div className="flex-shrink-0">
                                        <span className="inline-flex items-center gap-1.5 bg-white/10 border border-white/20 text-white text-xs font-medium px-3 py-2 rounded-lg">
                                            Results will be revealed soon
                                        </span>
                                    </div>
                                </div>

                                {/* Stats Bar */}
                                <div className="flex flex-wrap gap-4 mb-6">
                                    <div className="flex items-center gap-2 bg-white/5 rounded-lg px-3 py-2 border border-white/10">
                                        <MdPerson className="w-4 h-4 text-gov-gold-400" />
                                        <span className="text-white/90 text-sm">
                                            <span className="font-semibold text-white">{election.total_voters}</span> votes cast
                                        </span>
                                    </div>
                                    <div className="flex items-center gap-2 bg-white/5 rounded-lg px-3 py-2 border border-white/10">
                                        <MdLeaderboard className="w-4 h-4 text-gov-gold-400" />
                                        <span className="text-white/90 text-sm">
                                            <span className="font-semibold text-white">{election.positions.length}</span> positions
                                        </span>
                                    </div>
                                </div>

                                {/* Positions and Candidates */}
                                <div className="space-y-6">
                                    {election.positions.length > 0 ? (
                                        election.positions.map((position, posIndex) => (
                                            <div key={position.position_id} className="bg-white/5 rounded-xl p-4 sm:p-5 border border-white/10">
                                                <h4 className="text-lg sm:text-xl font-semibold text-white mb-4 flex items-center gap-2">
                                                    <MdHowToVote className="w-5 h-5 text-gov-gold-400" />
                                                    {position.position_name}
                                                </h4>
                                                
                                                {/* Candidates Grid */}
                                                <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 sm:gap-4">
                                                    {position.candidates.map((candidate, candIndex) => (
                                                        <CandidateCard 
                                                            key={candidate.id}
                                                            candidate={candidate}
                                                            totalVotes={position.total_votes}
                                                            rank={candIndex + 1}
                                                            isLeader={candIndex === 0 && position.total_votes > 0}
                                                            isLive={true}
                                                        />
                                                    ))}
                                                </div>
                                            </div>
                                        ))
                                    ) : (
                                        <div className="text-center py-8 text-white/60">
                                            <MdHowToVote className="w-12 h-12 mx-auto mb-3 opacity-50" />
                                            <p>No candidates registered yet</p>
                                        </div>
                                    )}
                                </div>
                            </motion.div>
                        ))}

                        {/* Completed Elections */}
                        {completedElections.length > 0 && ongoingElections.length > 0 && (
                            <div className="flex items-center gap-4 mt-8">
                                <div className="flex-1 h-px bg-white/20"></div>
                                <span className="text-white/70 text-sm font-medium">Recently Completed</span>
                                <div className="flex-1 h-px bg-white/20"></div>
                            </div>
                        )}

                        {completedElections.map((election, electionIndex) => (
                            <motion.div
                                key={election.id}
                                initial={{ opacity: 0, y: 30 }}
                                whileInView={{ opacity: 1, y: 0 }}
                                viewport={{ once: true }}
                                transition={{ duration: 0.5, delay: electionIndex * 0.1 }}
                                className="bg-white/10 backdrop-blur-sm rounded-2xl p-5 sm:p-6 lg:p-8 border border-white/10 shadow-lg"
                            >
                                {/* Election Header */}
                                <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 pb-4 border-b border-white/10">
                                    <div>
                                        <div className="flex items-center gap-2 mb-2">
                                            <span className="inline-flex items-center gap-1.5 bg-gov-gold-500/20 border border-gov-gold-400/30 text-gov-gold-400 text-xs font-semibold px-2.5 py-1 rounded-full">
                                                <span className="text-sm">🏆</span>
                                                RESULTS REVEALED
                                            </span>
                                        </div>
                                        <h3 className="text-xl sm:text-2xl font-bold text-white mb-1">
                                            {election.election_name}
                                        </h3>
                                        {election.organization && (
                                            <p className="text-white/70 text-sm">{election.organization}</p>
                                        )}
                                        <p className="text-white/60 text-xs mt-1">
                                            Election ended: {election.ended_at}
                                        </p>
                                    </div>
                                </div>

                                {/* Stats Bar */}
                                <div className="flex flex-wrap gap-4 mb-6">
                                    <div className="flex items-center gap-2 bg-white/5 rounded-lg px-3 py-2 border border-white/10">
                                        <MdPerson className="w-4 h-4 text-gov-gold-400" />
                                        <span className="text-white/90 text-sm">
                                            <span className="font-semibold text-white">{election.total_voters}</span> total voters
                                        </span>
                                    </div>
                                    <div className="flex items-center gap-2 bg-white/5 rounded-lg px-3 py-2 border border-white/10">
                                        <MdLeaderboard className="w-4 h-4 text-gov-gold-400" />
                                        <span className="text-white/90 text-sm">
                                            <span className="font-semibold text-white">{election.positions.length}</span> positions
                                        </span>
                                    </div>
                                </div>

                                {/* Positions and Candidates */}
                                <div className="space-y-6">
                                    {election.positions.length > 0 ? (
                                        election.positions.map((position, posIndex) => (
                                            <div key={position.position_id} className="bg-white/5 rounded-xl p-4 sm:p-5 border border-white/10">
                                                <h4 className="text-lg sm:text-xl font-semibold text-white mb-4 flex items-center gap-2">
                                                    <MdHowToVote className="w-5 h-5 text-gov-gold-400" />
                                                    {position.position_name}
                                                </h4>
                                                
                                                {/* Candidates Grid */}
                                                <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 sm:gap-4">
                                                    {position.candidates.map((candidate, candIndex) => (
                                                        <CandidateCard 
                                                            key={candidate.id}
                                                            candidate={candidate}
                                                            totalVotes={position.total_votes}
                                                            rank={candIndex + 1}
                                                            isLeader={candIndex === 0 && position.total_votes > 0}
                                                            isLive={false}
                                                        />
                                                    ))}
                                                </div>
                                            </div>
                                        ))
                                    ) : (
                                        <div className="text-center py-8 text-white/60">
                                            <MdHowToVote className="w-12 h-12 mx-auto mb-3 opacity-50" />
                                            <p>No candidates were registered</p>
                                        </div>
                                    )}
                                </div>
                            </motion.div>
                        ))}

                        {/* Subtle refresh indicator */}
                        <div className="text-center pt-4">
                            <span className="inline-flex items-center gap-1.5 text-white/50 text-xs">
                                <span className="w-1.5 h-1.5 rounded-full bg-gov-gold-400 animate-pulse" />
                                Updates automatically
                            </span>
                        </div>
                    </div>
                    )}
                </motion.div>
            </div>
        </section>
    );
}

// Candidate Card Component - Dark green card theme (white text, translucent panels, gold accents)
function CandidateCard({ candidate, totalVotes, rank, isLeader, isLive }) {
    const percentage = totalVotes > 0 ? ((candidate.votes_count / totalVotes) * 100).toFixed(1) : 0;
    const isAnonymous = candidate.is_anonymous;
    
    return (
        <motion.div
            initial={{ opacity: 0, scale: 0.9 }}
            animate={{ opacity: 1, scale: 1 }}
            layout
            className={`
                relative bg-white/10 rounded-xl p-3 sm:p-4 text-center border border-white/10
                ${isLeader ? 'ring-2 ring-gov-gold-400 bg-gov-gold-500/10' : ''}
                transition-all duration-300 hover:bg-white/15
            `}
        >
            {/* Winner/Leader Badge */}
            {isLeader && (
                <motion.div 
                    initial={{ scale: 0 }}
                    animate={{ scale: 1 }}
                    className="absolute -top-2 -right-2 w-7 h-7 bg-gov-gold-500 rounded-full flex items-center justify-center shadow-lg z-10"
                >
                    {isAnonymous ? (
                        <HiTrendingUp className="w-4 h-4 text-gov-green-900" />
                    ) : (
                        <span className="text-gov-green-900 text-xs font-bold">1st</span>
                    )}
                </motion.div>
            )}
            
            {/* Live indicator for ongoing elections */}
            {isLive && (
                <div className="absolute -top-1 -left-1 w-3 h-3 bg-green-500 rounded-full animate-pulse shadow-lg shadow-green-500/50" />
            )}
            
            {/* Photo - Anonymous or Revealed */}
            <AnimatePresence mode="wait">
                {isAnonymous ? (
                    <motion.div
                        key="anonymous"
                        initial={{ opacity: 0, rotateY: 90 }}
                        animate={{ opacity: 1, rotateY: 0 }}
                        exit={{ opacity: 0, rotateY: -90 }}
                        className="w-16 h-16 sm:w-20 sm:h-20 mx-auto mb-3 bg-gray-600 rounded-full flex items-center justify-center shadow-lg relative overflow-hidden"
                    >
                        <HiQuestionMarkCircle className="w-10 h-10 sm:w-12 sm:h-12 text-white/70" />
                        {isLive && (
                            <div className="absolute inset-0 bg-gradient-to-t from-gov-gold-500/20 to-transparent animate-pulse" />
                        )}
                    </motion.div>
                ) : (
                    <motion.div
                        key="revealed"
                        initial={{ opacity: 0, rotateY: 90, scale: 0.8 }}
                        animate={{ opacity: 1, rotateY: 0, scale: 1 }}
                        transition={{ duration: 0.6, ease: 'easeOut' }}
                        className={`w-16 h-16 sm:w-20 sm:h-20 mx-auto mb-3 rounded-full shadow-lg overflow-hidden ${isLeader ? 'ring-4 ring-gov-gold-400' : 'ring-2 ring-white/20'}`}
                    >
                        {candidate.photo ? (
                            <img 
                                src={candidate.photo} 
                                alt={candidate.name}
                                className="w-full h-full object-cover"
                                onError={(e) => {
                                    e.target.style.display = 'none';
                                    e.target.nextSibling.style.display = 'flex';
                                }}
                            />
                        ) : null}
                        <div 
                            className={`w-full h-full bg-gradient-to-br from-gov-green-600 to-gov-green-800 items-center justify-center ${candidate.photo ? 'hidden' : 'flex'}`}
                        >
                            <span className="text-2xl font-bold text-white">
                                {candidate.name?.charAt(0)?.toUpperCase() || '?'}
                            </span>
                        </div>
                    </motion.div>
                )}
            </AnimatePresence>
            
            {/* Name - Anonymous or Revealed */}
            <AnimatePresence mode="wait">
                <motion.h5
                    key={isAnonymous ? 'anon-name' : 'real-name'}
                    initial={{ opacity: 0, y: 10 }}
                    animate={{ opacity: 1, y: 0 }}
                    exit={{ opacity: 0, y: -10 }}
                    className={`font-bold text-white text-sm sm:text-base mb-1 truncate ${!isAnonymous && isLeader ? 'text-gov-gold-400' : ''}`}
                    title={candidate.name}
                >
                    {candidate.name}
                </motion.h5>
            </AnimatePresence>
            
            {/* Vote Count */}
            <div className="mt-2">
                <motion.p 
                    key={candidate.votes_count}
                    initial={{ scale: 1.1 }}
                    animate={{ scale: 1 }}
                    className={`text-xl sm:text-2xl font-bold ${isLeader && !isAnonymous ? 'text-gov-gold-400' : 'text-gov-gold-400'}`}
                >
                    {candidate.votes_count.toLocaleString()}
                </motion.p>
                <p className="text-white/60 text-xs">{isAnonymous ? 'votes' : 'final votes'}</p>
            </div>
            
            {/* Progress Bar */}
            <div className="mt-3 h-2 bg-white/10 rounded-full overflow-hidden">
                <motion.div
                    initial={{ width: 0 }}
                    animate={{ width: `${percentage}%` }}
                    transition={{ duration: 0.8, ease: 'easeOut' }}
                    className={`h-full rounded-full ${isLeader ? 'bg-gradient-to-r from-gov-gold-400 to-gov-gold-500' : 'bg-white/40'}`}
                />
            </div>
            <p className="text-white/70 text-xs mt-1">{percentage}%</p>
            
            {/* Rank Badge for revealed candidates */}
            {!isAnonymous && rank <= 3 && (
                <motion.div
                    initial={{ opacity: 0, scale: 0 }}
                    animate={{ opacity: 1, scale: 1 }}
                    transition={{ delay: 0.3 }}
                    className={`absolute -bottom-1 left-1/2 -translate-x-1/2 px-2 py-0.5 rounded-full text-xs font-bold ${
                        rank === 1 ? 'bg-gov-gold-500 text-gov-green-900' :
                        rank === 2 ? 'bg-gray-300 text-gray-800' :
                        'bg-amber-600 text-white'
                    }`}
                >
                    {rank === 1 ? '🏆 Winner' : rank === 2 ? '2nd' : '3rd'}
                </motion.div>
            )}
        </motion.div>
    );
}

